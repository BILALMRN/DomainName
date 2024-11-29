<?php

namespace Paymenter\Extensions\Others\DomainName\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Paymenter\Extensions\Others\DomainName\Models\Domain as ModelsDomain;

class Domain extends Component
{
    public $domainName = '';
    public $activeComponent = 'ListDomains';


    public function mount()
    {
        $this->domainName = '';
        $this->activeComponent = 'ListDomains';
    }

    public function setActiveComponent($activeComponent, $domainName)
    {
        $this->domainName = $domainName;
        $this->activeComponent = $activeComponent;
    }


    public function render()
    {
        return view('liveware::domain', [
            'domains' => ModelsDomain::query()->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Domain',
        ]);
    }
}
