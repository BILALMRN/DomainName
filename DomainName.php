<?php

namespace Paymenter\Extensions\Others\DomainName;

use App\Classes\Extension\Extension;
use App\Events\Invoice\Paid;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Paymenter\Extensions\Others\DomainName\Admin\Resources\DomainNameResource;
use Paymenter\Extensions\Others\DomainName\Registers\Helper;
use App\Models\Extension as ModelsExtension;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Paymenter\Extensions\Others\DomainName\Listeners\DomainInvoicePaidListener;

final class DomainName extends Extension
{
    public function getConfig($values = [])
    {

        try {
            $mainConfig = [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('To create a new domain, go to <a class="text-primary-600" href="' . DomainNameResource::getUrl() . '">Domains</a>.'),
                ],
            ];
            return array_merge(Helper::getRegister($this->config('register_name'))->getConfig(), $mainConfig);
        } catch (\Exception $e) {
            // If domainRegester resource is not installed, return
            return [
                [
                    'name' => 'register_name',
                    'label' => 'Register',
                    'description' => 'Select a register',
                    'type' => 'select',
                    'multiple' => false,
                    'options' => Helper::getRegisters(),
                    'required' => true,
                ],
                [
                    'name' => 'price_setup_domain',
                    'label' => 'Price Setup Domain $ per order', // is mean how profit earn by eatch domain order
                    'description' => 'This represents the profit earned for each domain purchase.',
                    'type' => 'number',
                    'required' => true,
                ],
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('You\'ll need to enable this extension above to get started.'),
                ],
            ];
        }
    }

    public function enabled()
    {
        // Run migrations
        Artisan::call('migrate', ['--path' => 'extensions/Others/DomainName/database/migrations/2024_11_30_103407_create_domains_table.php']);
    }

    public function boot()
    {
        $isProductDomainConfigured =  Product::where('slug', 'domain-name')
            ->whereHas('category', function (Builder $query) {
                $query->where('slug', 'domain')
                    ->where('name', 'Domain');
            })
            ->exists();

        if (!$isProductDomainConfigured || !Helper::getRegister()::isConfigurable()) {
            return;
        }

        // Register views
        View::addNamespace('DomainName', __DIR__ . '/resources/views');
        View::addNamespace('liveware', __DIR__ . '/resources/views/liveware-component');

        // livewire component
        \Livewire\Livewire::component('searchDomain', \Paymenter\Extensions\Others\DomainName\Livewire\SearchDomain::class);
        \Livewire\Livewire::component('domain', \Paymenter\Extensions\Others\DomainName\Livewire\Domain::class);
        \Livewire\Livewire::component('changeNameservers', \Paymenter\Extensions\Others\DomainName\Livewire\ChangeNameservers::class);
        \Livewire\Livewire::component('updateDomainInfo', \Paymenter\Extensions\Others\DomainName\Livewire\UpdateDomainInfo::class);

        // Event listeners
        Event::listen('pages.home', function () {
            return [
                'view' => view('DomainName::home-searchDomain'),
            ];
        });

        Event::listen('pages.dashboard', function () {
            return [
                'view' => view('DomainName::dashboard-domains'),
            ];
        });
        Event::listen('pages.dashboard.buttons', function () {
            return [
                'view' => view('DomainName::dashboard-button'),
            ];
        });

        Event::listen(Paid::class, DomainInvoicePaidListener::class);
    }

    public static function getConfigValue(string $key)
    {
        return ModelsExtension::where('extension', class_basename(static::class))->first()->settings->pluck('value', 'key')->toArray()[$key] ?? null;;
    }
}
