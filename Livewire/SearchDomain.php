<?php

namespace Paymenter\Extensions\Others\DomainName\Livewire;

use App\Classes\Cart;
use App\Classes\Price;
use App\Helpers\ExtensionHelper;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Paymenter\Extensions\Others\DomainName\DomainName;
use Paymenter\Extensions\Others\DomainName\Registers\Helper;
use Paymenter\Extensions\Others\DomainName\Registers\IRegister;

class SearchDomain extends Component
{
    public $search = '';
    public $domains = [];
    private static ?IRegister $staticRegister = null;


    protected $rules = [
        'search' => 'required|string|min:3',
    ];

    public function __construct()
    {
        self::$staticRegister = Helper::getRegister();
    }

    public function mount()
    {
        if (self::$staticRegister === null) {
            self::$staticRegister = Helper::getRegister();
        }

        if (self::$staticRegister === null) {
            throw new \Exception('Register service not initialized.');
        }
    }

    public function searchDomain()
    {
        $this->validate();
        // Replace with actual service call

        $result = self::$staticRegister->searchDomain($this->search);
        $this->domains = $result ?? [];
    }

    public function addToCart($domainName)
    {
        $domain = self::$staticRegister->getDomain($domainName);
        if ($domain === null || !$domain['available'] || !is_numeric($domain['price']) || $domain['price'] < 0) {
            return redirect()->back()->with('error', 'Domain not available.');
        }

        $product = Product::where('slug', 'domain-name')
            ->whereHas('category', function (Builder $query) {
                $query->where('slug', 'domain')
                    ->where('name', 'Domain');
            })->firstOrFail();

        $checkoutConfig = ExtensionHelper::getCheckoutConfig($product);
        $product->name = $domain['domain'];
        $plan = $product->plans->first();

        $product->configOptions[0]->value = $domain['domain'];

        $domainPrice = (float)$domain['price'];
        $price = new Price((object) [
            'price' => $domainPrice,
            'currency' => $plan->price()->currency,
            'setup_fee' => $plan->price()->setup_fee,
        ], false, false);

        Cart::add($product, $plan, $this->mapConfigOptions($product), $checkoutConfig, $price, 1, $product->id);
        return redirect()->route('cart')->with('success', 'Domain added to cart.');
    }



    private function mapConfigOptions($product)
    {
        return $product->configOptions->map(function ($option) use ($product) {
            // Get the selected value for each option from product's config options (if available)
            $configOptions = $product->configOptions->pluck('value', 'id')->toArray();

            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return (object) [
                    'option_id' => $option->id,
                    'option_name' => $option->name,
                    'option_type' => $option->type,
                    'option_env_variable' => $option->env_variable,
                    'value' => $configOptions[$option->id] ?? null,
                    'value_name' => $configOptions[$option->id] ?? null,
                ];
            }

            // If the option has children, find the selected one by ID
            $childOption = $option->children->where('id', $configOptions[$option->id] ?? null)->first();

            return (object) [
                'option_id' => $option->id,
                'option_name' => $option->name,
                'option_type' => $option->type,
                'option_env_variable' => $option->env_variable,
                'value' => $configOptions[$option->id] ?? null,
                'value_name' => $childOption ? $childOption->name : null,
            ];
        });
    }


    public function render()
    {
        return view('liveware::searchDomain', [
            'domains' => $this->domains
        ]);
    }
}
