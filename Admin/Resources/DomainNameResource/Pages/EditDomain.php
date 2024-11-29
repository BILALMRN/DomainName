<?php

namespace Paymenter\Extensions\Others\DomainName\Admin\Resources\DomainNameResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\DomainName\Admin\Resources\DomainNameResource;
use Paymenter\Extensions\Others\DomainName\Models\Domain;
use Paymenter\Extensions\Others\DomainName\Registers\Helper;

class EditDomain extends EditRecord
{
    protected static string $resource = DomainNameResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Your custom logic here before saving 
        Helper::getRegister()->changeNameservers($data['domain'], [$data['ns1'], $data['ns2'], $data['ns3'], $data['ns4']]);
        if ($data['user_id'] !== Domain::where('domain', $data['domain'])->first()->user_id) {
            $user = User::find($data['user_id']);
            if ($user) {
                Helper::getRegister()->updateDomainInfo($data['domain'], $user);
            }
        }
        return $data;
    }
}
