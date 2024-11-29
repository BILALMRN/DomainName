<div>
    <div x-data="{ activeComponent: @entangle('activeComponent') }">

        <div x-show="activeComponent === 'changeNameservers'" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            @if($activeComponent == 'changeNameservers')
            @livewire('changeNameservers', ['domainName' => $domainName])
            @endif
        </div>
        <div x-show="activeComponent === 'updateDomainInfo'" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            @if($activeComponent == 'updateDomainInfo')
            @livewire('updateDomainInfo', ['domainName' => $domainName])
            @endif
        </div>
        <div x-show="activeComponent === 'ListDomains'" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            @if($activeComponent === 'ListDomains')
            <!-- ListDomain Component -->
            <div class="bg-primary-800 p-6 rounded-lg mt-2">
                <div class="flex flex-col md:flex-row justify-between">
                    <h1 class="text-2xl font-semibold text-white">Domains</h1>
                </div>
                <div class="w-full overflow-x-auto mt-2">

                    <table class="w-full border-spacing-y-2.5 border-separate">
                        <thead>
                            <tr>
                                <th class="text-left pl-2">id</th>
                                <th class="text-left">domain Name</th>
                                <th class="text-left">domain Register</th>
                                <th class="text-left">change Nameservers</th>
                                <th class="text-left">Update Domain Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($domains as $domain)
                            <tr class="bg-primary-700 text-white hover:shadow-xl transition duration-300">
                                <td class="p-2 rounded-l-md">#{{ $domain->id }}</td>
                                <td>{{ $domain->domain }}</td>
                                <td>
                                    <span
                                        class="font-semibold p-1 px-1.5 rounded-md text-green-500">
                                        {{ ucfirst($domain->register_name) }}
                                    </span>
                                </td>
                                <td class="p-1 rounded-r-md">
                                    <x-button.primary class="h-fit !w-fit"
                                        wire:click="setActiveComponent('changeNameservers', '{{ $domain->domain }}')">
                                        <h4 class="text-lg font-semibold text-white">change Nameservers</h4>
                                    </x-button.primary>
                                </td>
                                <td class="p-1 rounded-r-md">
                                    <x-button.primary class="h-fit !w-fit"
                                        wire:click="setActiveComponent('updateDomainInfo', '{{ $domain->domain }}')">
                                        <h4 class="text-lg font-semibold text-white"> Update Domain Info</h4>
                                    </x-button.primary>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End ListDomain Component -->
            @endif
        </div>
    </div>


</div>