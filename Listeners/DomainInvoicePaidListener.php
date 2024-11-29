<?php

namespace Paymenter\Extensions\Others\DomainName\Listeners;

use App\Events\Invoice\Paid;
use App\Models\Property;
use App\Models\Service;
use Paymenter\Extensions\Others\DomainName\DomainName;
use Paymenter\Extensions\Others\DomainName\Models\Domain;
use Paymenter\Extensions\Others\DomainName\Registers\Helper;

class DomainInvoicePaidListener
{
    /**
     * Handle the event.
     */
    public function handle(Paid $event): void
    {

        $user = $event->invoice->user;
        $event->invoice->items->each(function ($item) use ($user) {
            $property = Property::select('model_id', 'value')
                ->where('model_type', 'App\Models\Service')
                ->where('model_id', $item->reference_id)
                ->where('key', 'domain')->first();

            if (!$property) {
                return;
            }
            $domainExist = Domain::where('domain', $property->value)->first();
            // dd($domainExist);
            if ($domainExist) {
                $isWillExpire = Service::where('id', $property->module_id)->where('expires_at', '<', now()->addMonths(3))->exist();
                if ($isWillExpire && $domainExist->register_name == DomainName::getConfigValue('register_name') && $domainExist->user_id == $user->id && $item) {
                    // Renew the domain
                    try {
                        Helper::getRegister()->renewDomain($property->value);
                        // Todo : implement if not renewDomain work
                    } catch (\Exception $e) {
                    }
                }
            } elseif (Helper::getRegister()->registerDomain($property->value, $user)) {
                $domain = new Domain([
                    'user_id' => $user->id,
                    'service_id' => $property->model_id,
                    'domain' => $property->value,
                    'register_name' => DomainName::getConfigValue('register_name'),
                ]);
                $domain->save();
            }
        });
    }
}
