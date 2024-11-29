<?php

namespace Paymenter\Extensions\Others\DomainName\Livewire;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Paymenter\Extensions\Others\DomainName\DomainName;
use Paymenter\Extensions\Others\DomainName\Registers\Helper;

class UpdateDomainInfo extends Component
{

    public $theCheckBox;
    public $domainName;

    public function mount($domainName)
    {
        $this->domainName = $domainName;
    }


    public function updateDomainInfo()
    {

        if (Helper::getRegister()->updateDomainInfo($this->domainName, Auth::user())) {
            return redirect()->to(route('dashboard'))->with('success', 'Domain info updated successfully');
        } else {
            return redirect(route('dashboard'))->with('error', 'Error updating domain info');
        }
    }


    public function render()
    {
        return view('liveware::updateDomainInfo', [
            'domainName' => $this->domainName,
            'user' => Auth::user(),
            'property' => Property::where('model_type', 'App\Models\User')->where('model_id', Auth::user()->id)->whereIn('key', ['address', 'city', 'state', 'zip', 'country', 'phone'])->pluck('value', 'key')->toArray()
        ]);
    }
}
