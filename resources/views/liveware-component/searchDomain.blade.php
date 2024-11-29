<div class="overflow-x-auto w-full">
    <form class="mx-auto flex flex-row gap-2 mt-4 shadow-sm px-6 sm:px-14 pb-10 bg-primary-800 rounded-md w-full" id="searchDomain" wire:submit.prevent="searchDomain">
        @csrf
        <div class="form-group w-full">
            <label for="domain_name">Search for a Domain Name:</label>
            <x-form.input name="domain_name" type="text" :label="__('Domain Name:')" :placeholder="__('Enter domain name')" wire:model="search" required />
        </div>
        <x-button.primary class="xl:max-w-[10%] max-w-[100px] mt-8" type="submit">Search</x-button.primary>
    </form>

    <div class="overflow-x-auto w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-4">

        <ul class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            @foreach($domains as $domain)
            @if (!empty($domain))
            <li class="border-b flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <span class="px-4 py-3 font-medium whitespace-nowrap">{{ $domain['domain'] ?? 'N/A' }}</span>
                <span class="px-4 py-3">${{ $domain['available'] ? $domain['price'] : 'N/A' }}</span>
                <span class="px-4 py-3 text-{{ $domain['available'] ? 'green' : 'red' }}-500">
                    {{ $domain['available'] ? 'Available' : 'Unavailable' }}
                </span>
                <span class="px-4 py-3">
                    @if($domain['available'] && $domain['price'] > 0)
                    <button wire:click="addToCart('{{ $domain['domain'] }}')" type="button" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        Add To Cart
                    </button>
                    @endif
                </span>
            </li>
            @endif
            @endforeach
        </ul>
        @if(strlen($search) > 0 && count($domains) === 0)
        <p>No domains found for "{{ $search }}"</p>
        @endif
    </div>
</div>