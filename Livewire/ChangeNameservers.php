<?php

namespace Paymenter\Extensions\Others\DomainName\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Paymenter\Extensions\Others\DomainName\DomainName;
use Paymenter\Extensions\Others\DomainName\Models\Domain;
use Paymenter\Extensions\Others\DomainName\Registers\Helper;

class ChangeNameservers extends Component
{
    #[Locked]
    public $domainName;
    public $nameserver1;
    public $nameserver2;
    public $nameserver3;
    public $nameserver4;

    protected $rules = [
        'nameserver1' => 'required|string|max:255',
        'nameserver2' => 'required|string|max:255',
        'nameserver3' => 'nullable|string|max:255',
        'nameserver4' => 'nullable|string|max:255',
    ];

    public function mount($domainName)
    {
        $this->domainName = $domainName;
        $domain = Domain::where('domain', $domainName)->first();
        $this->nameserver1 = $domain->ns1 ?? '';
        $this->nameserver2 = $domain->ns2 ?? '';
        $this->nameserver3 = $domain->ns3  ?? '';
        $this->nameserver4 = $domain->ns4  ?? '';
    }

    public function changeNameservers()
    {
        $nameservers = array_filter([
            $this->nameserver1,
            $this->nameserver2,
            $this->nameserver3,
            $this->nameserver4,
        ]);

        $domainName = $this->domainName;

        if (Helper::getRegister()->changeNameservers($domainName, $nameservers)) {
            Domain::where("domain", $domainName)
                ->where("user_id", Auth::id())
                ->first()
                ->update([
                    'ns1' => $this->nameserver1,
                    'ns2' => $this->nameserver2,
                    'ns3' => $this->nameserver3,
                    'ns4' => $this->nameserver4,
                ]);
        }

        return redirect()->route('dashboard');
    }


    public function render()
    {
        return view('liveware::changeNameservers', [
            'domainName' => $this->domainName
        ]);
    }
}
