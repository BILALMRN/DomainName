<?php

namespace Paymenter\Extensions\Others\DomainName\Registers\Namecheap;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Others\DomainName\Registers\IRegister;
use Paymenter\Extensions\Others\DomainName\DomainName;

final class Namecheap implements IRegister
{
    private $apiUser;
    private $apiKey;
    private $username;
    private $clientIp;
    private $endpoint;

    public function __construct()
    {
        $this->apiKey   = DomainName::getConfigValue('apiKey');
        $this->username = DomainName::getConfigValue('username');
        $this->apiUser  =  DomainName::getConfigValue('username');
        $this->clientIp = request()->ip();
        $this->endpoint = 'https://api.sandbox.namecheap.com/xml.response';
    }

    public function getConfig(): array
    {

        $baseConfig = [
            [
                'name' => 'apiKey',
                'label' => 'API Key',
                'type' => 'text',
                'description' => 'Your Namecheap API key',
                'required' => true,
            ],
            [
                'name' => 'username',
                'label' => 'Username',
                'type' => 'text',
                'description' => 'Your Namecheap account username',
                'required' => true,
            ],
            [
                'name' => 'namecheap_default_tld',
                'label' => 'Default TLD For search',
                'description' => 'Select a TLD to use it by default for search',
                'type' => 'select',
                'multiple' => true,
                'options' => array_keys($this->getTLD_Price()),
                'required' => true,
            ],
            [
                'name' => 'AddFreeWhoisguard',
                'label' => 'Add Free Whoisguard',
                'type' => 'checkbox',
                'description' => 'Add free Whoisguard protection',
                'required' => false,
            ],
        ];



        if (!app()->environment('production')) {
            $baseConfig[] = [
                'name' => 'testMode',
                'label' => 'Test Mode',
                'type' => 'checkbox',
                'description' => 'Enable test mode for sandbox environment',
                'required' => false,
            ];
        }
        return $baseConfig;
    }

    public static function isConfigurable(): bool
    {
        return DomainName::getConfigValue('apiKey') !== null &&
            DomainName::getConfigValue('username') !== null;
    }

    public function searchDomain(string $domainName): array
    {

        $defaultTld = json_decode(DomainName::getConfigValue('namecheap_default_tld'), true) ?? [];
        $sld = $this->getSld($domainName);
        $tldDomain = $this->getTld($domainName);
        $domainsToCheck = $tldDomain ? [$domainName] : [];
        foreach ($defaultTld as $key) {
            if ($tldDomain == $key) continue;
            $domainsToCheck[] = $sld . $key;
        }
        $params = $this->prepareParams('namecheap.domains.check', [
            'DomainList' => implode(',', $domainsToCheck),
        ]);

        $response = $this->makeRequest($params);
        $theData = $response['CommandResponse']['DomainCheckResult'] ?? [];

        if (isset($theData['@attributes'])) {
            $theData = [$theData];
        }
        $result = [];
        $setupPrice = is_numeric(DomainName::getConfigValue('price_setup_domain')) ? (float)DomainName::getConfigValue('price_setup_domain') : 0;
        foreach ($theData as $data) {
            $data = $data['@attributes'];
            $tldPart = '.' . $this->getTld($data['Domain']);
            $price = $data['Domain'] == $domainName  ? $this->getPricing($this->getTld($data['Domain'])) : ($this->getTLD_Price()[$tldPart] ?? null);
            $result[] = [
                'domain' => $data['Domain'],
                'price' => $data['IsPremiumName'] === 'true' ?  (float)$data['PremiumRegistrationPrice'] + $setupPrice : (float)$price + $setupPrice,
                'available' => $data['Available'] === 'true' ? true : false,
            ];
        }
        return $result;
    }

    private function getPricing(string $productName): ?string
    {
        $params = $this->prepareParams('namecheap.users.getPricing', [
            'ProductType' => 'DOMAIN',
            'ProductCategory' => 'DOMAINS',
            'ActionName' => 'RENEW',
            'ProductName' => $productName,
        ]);

        $response = $this->makeRequest($params);
        // Extract the pricing information from the response
        $price = $response['CommandResponse']['UserGetPricingResult']['ProductType']['ProductCategory']['Product']['Price'][0]['@attributes']['RegularPrice'] ?? null;

        return $price;
    }



    public function getDomain(string $domainName): array
    {
        $params = $this->prepareParams('namecheap.domains.check', [
            'DomainList' => $domainName,
        ]);

        $response = $this->makeRequest($params);

        $theData = $response['CommandResponse']['DomainCheckResult'] ?? [];

        if (isset($theData['@attributes'])) {
            $theData = [$theData];
        }
        $setupPrice = is_numeric(DomainName::getConfigValue('price_setup_domain')) ? (float)DomainName::getConfigValue('price_setup_domain') : 0;
        foreach ($theData as $data) {
            $data = $data['@attributes'];
            if ($data['Domain'] == $domainName) {
                return [
                    'domain' => $data['Domain'],
                    'price' => $data['IsPremiumName'] === 'true' ?  (float)$data['PremiumRegistrationPrice'] + $setupPrice : (float)$this->getPricing($this->getTld($data['Domain']))  + $setupPrice,
                    'available' => $data['Available'] === 'true' ? true : false,
                ];
            }
        }

        return [];
    }

    public function registerDomain(string $domainName, User $user, int $years = 1): bool
    {
        $data = $this->prepareUserData($user);
        $adminProperties = $this->getAdminProperties();

        $AddFreeWhoisguard = DomainName::getConfigValue('AddFreeWhoisguard') === 'true' ? true : false;
        $params = $this->prepareParams('namecheap.domains.create', array_merge([
            'DomainName' => $domainName,
            'Years' => $years,
            'AddFreeWhoisguard' => $AddFreeWhoisguard,
            'WGEnabled' => $AddFreeWhoisguard,
        ], $data, $adminProperties));
        return $this->makeRequest($params)['CommandResponse']['DomainCreateResult']['@attributes']['Registered'] === 'true';
    }

    public function renewDomain(string $domainName, int $years = 1): bool
    {
        $params = $this->prepareParams('namecheap.domains.renew', [
            'DomainName' => $domainName,
            'Years' => $years,
        ]);

        return $this->makeRequest($params)['CommandResponse']['DomainRenewResult']['@attributes']['Renew'] === 'true';
    }

    public function updateDomainInfo(string $domainName, User $user): bool
    {
        $data = array_merge($this->prepareUserData($user), $this->getAdminProperties());
        $params = $this->prepareParams('namecheap.domains.setContacts', array_merge([
            'DomainName' => $domainName,
        ], $data));
        return $this->makeRequest($params)['CommandResponse']['DomainSetContactsResult']['@attributes']['IsSuccess'] === 'true';
    }

    public function changeNameservers(string $domainName, array $nameservers): bool
    {
        $params = $this->prepareParams('namecheap.domains.dns.setCustom', [
            'SLD' => $this->getSld($domainName),
            'TLD' => $this->getTld($domainName),
            'NameServers' => implode(',', $nameservers),
        ]);

        return $this->makeRequest($params)['CommandResponse']['DomainDNSSetCustomResult']['@attributes']['Updated'] === 'true';
    }



    public function enableIdProtection(string $domainName): bool
    {
        $params = $this->prepareParams('Namecheap.Whoisguard.enable', [
            'DomainName' => $domainName,
        ]);

        return $this->makeRequest($params)['CommandResponse']['WhoisguardEnableResult']['@attributes']['IsSuccess'] === 'true';
    }

    public function disableIdProtection(string $domainName): bool
    {
        $params = $this->prepareParams('Namecheap.Whoisguard.disable', [
            'DomainName' => $domainName,
        ]);

        return $this->makeRequest($params)['CommandResponse']['WhoisguardDisableResult']['@attributes']['IsSuccess'] === 'true';
    }

    private function prepareParams(string $command, array $extraParams = []): array
    {
        return array_merge([
            'ApiUser' => $this->apiUser,
            'ApiKey' => $this->apiKey,
            'UserName' => $this->username,
            'Command' => $command,
            'ClientIp' => $this->clientIp,
        ], $extraParams);
    }

    private function makeRequest(array $params): array
    {
        $response = Http::get($this->endpoint, $params);
        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->body());
        }
        return $this->xmlToArray($response->body());
    }

    private function xmlToArray(string $xml): array
    {
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        return json_decode($json, true);
    }

    private function prepareUserData(User $user): array
    {
        $properties = Property::where('model_type', 'App\Models\User')
            ->whereIn('key', ['address', 'city', 'state', 'zip', 'country', 'phone'])
            ->where('model_id', $user->id)->pluck('value', 'key')->toArray();
        // dd($properties['phone']);
        return [
            'RegistrantFirstName' => $user->first_name,
            'RegistrantLastName' => $user->last_name,
            'RegistrantAddress1' => $properties['address'] ?? 'N/A',
            'RegistrantCity' => $properties['city'] ?? 'N/A',
            'RegistrantStateProvince' => $properties['state'] ?? 'N/A',
            'RegistrantPostalCode' => $properties['zip'] ?? 'N/A',
            'RegistrantCountry' => $properties['country'] ?? 'N/A',
            'RegistrantPhone' => $properties['phone'] ?? 'N/A',
            'RegistrantEmailAddress' => $user->email,
            'AuxBillingFirstName' => $user->first_name,
            'AuxBillingLastName' => $user->last_name,
            'AuxBillingAddress1' => $properties['address'] ?? 'N/A',
            'AuxBillingCity' => $properties['city'] ?? 'N/A',
            'AuxBillingStateProvince' => $properties['state'] ?? 'N/A',
            'AuxBillingPostalCode' => $properties['zip'] ?? 'N/A',
            'AuxBillingCountry' => $properties['country'] ?? 'N/A',
            'AuxBillingPhone' => $properties['phone'] ?? 'N/A',
            'AuxBillingEmailAddress' => $user->email,
            'TechFirstName' => $user->first_name,
            'TechLastName' => $user->last_name,
            'TechAddress1' => $properties['address'] ?? 'N/A',
            'TechCity' => $properties['city'] ?? 'N/A',
            'TechStateProvince' => $properties['state'] ?? 'N/A',
            'TechPostalCode' => $properties['zip'] ?? 'N/A',
            'TechCountry' => $properties['country'] ?? 'N/A',
            'TechPhone' => $properties['phone'] ?? 'N/A',
            'TechEmailAddress' => $user->email,
        ];
    }

    private function getAdminProperties(): array
    {
        $admin = User::where('role_id', 1)->first();
        $properties = Property::where('model_type', 'App\Models\User')
            ->whereIn('key', ['address', 'city', 'state', 'zip', 'country', 'phone'])
            ->where('model_id', $admin->id)->pluck('value', 'key')->toArray();
        return [
            'AdminFirstName' => $admin->first_name ?? 'Paymenter',
            'AdminLastName' => $admin->last_name ?? 'Admin',
            'AdminAddress1' => $properties['address'] ?? 'N/A',
            'AdminCity' => $properties['city'] ?? 'N/A',
            'AdminStateProvince' => $properties['state'] ?? 'N/A',
            'AdminPostalCode' => $properties['zip'] ?? 'N/A',
            'AdminCountry' => $properties['country'] ?? 'N/A',
            'AdminPhone' => $properties['phone'] ?? 'N/A',
            'AdminEmailAddress' => $admin->email,
        ];
    }
    private function getSld(string $domainName): string
    {
        $parts = explode('.', $domainName);
        return $parts[0];
    }

    private function getTld(string $domainName): ?string
    {
        // Break the domain into parts
        $parts = explode('.', $domainName);

        // Handle single-part domains (e.g., example.com)
        if (count($parts) == 2) {
            return $parts[1];
        } elseif (count($parts) == 3) {
            // Handle multi-part TLDs (e.g., example.co.uk)
            return $parts[1] . '.' . $parts[2];
        }

        return null;
    }


    private function getTLD_Price()
    {
        require_once(__DIR__ . '/TLD_PRICE.php');
        return TLD_PRICE;
    }
}
