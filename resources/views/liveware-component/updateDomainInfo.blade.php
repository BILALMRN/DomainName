<div class="flex justify-center m-4">
    <div class="items-center bg-gray-50 rounded-lg shadow sm:flex dark:bg-gray-800 dark:border-gray-700">
        <img class="w-24 h-24 mr-3 mb-3 rounded-lg " src="{{$user->avatar}}">
        <div class="p-5">
            <h3 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                Name : {{ $user->first_name }} {{ $user->last_name }}
            </h3>
            <span class="text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
            <br>
            <p class="mt-3 mb-4 font-light text-gray-500 dark:text-gray-400"> Phone: {{ $property['phone'] }}</p>
            <p class="mt-3 mb-4 font-light text-gray-500 dark:text-gray-400"> Country: {{ $property['country'] }}</p>
            <p class="mt-3 mb-4 font-light text-gray-500 dark:text-gray-400"> City: {{ $property['city'] }}</p>
            <p class="mt-3 mb-4 font-light text-gray-500 dark:text-gray-400"> state: {{ $property['state'] }}</p>
            <p class="mt-3 mb-4 font-light text-gray-500 dark:text-gray-400"> Zip: {{ $property['zip'] }}</p>

        </div>
    </div>
    <hr>
    <div class="p-4 m-4 flex-clo shadow-md rounded">
        <h4 class="text-lg font-bold m-4">Update Domain Info</h4>
        <form class="mx-auto rounded-md w-full" id="updateDomainInfo" wire:submit.prevent="updateDomainInfo">
            @csrf
            <div class="m-4">
                <div class="flex items-center">
                    <input wire:model="theCheckBox" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="checked-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Yes, I have made changes to my personal information in my account, and I want to update the domain <span class="font-bold text-green-500"> [{{ $domainName }}] </span> info with this information.</label>
                </div>
                <x-button.primary class="m-6" type="submit">Change Info</x-button.primary>
            </div>
        </form>
    </div>