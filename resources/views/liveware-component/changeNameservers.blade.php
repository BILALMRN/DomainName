<div class="m-4 p-4 shadow-md rounded">
    <h4>Change Nameservers for a Domain : {{ $domainName }}</h4>
    <form class="mx-auto flex flex-row gap-2 mt-4 shadow-sm px-6 sm:px-14 pb-10 bg-primary-800 rounded-md w-full" id="changeNameservers" wire:submit.prevent="changeNameservers">
        @csrf
        <div class="form-group w-full">
            <label for="nameserver1">Name Server 1:</label>
            <x-form.input name="nameserver1" type="text" :label="__('Name Server 1:')" :placeholder="__('Enter name server 1')" wire:model="nameserver1" required />
        </div>
        <div class="form-group w-full">
            <label for="nameserver2">Name Server 2:</label>
            <x-form.input name="nameserver2" type="text" :label="__('Name Server 2:')" :placeholder="__('Enter name server 2')" wire:model="nameserver2" required />
        </div>
        <div class="form-group w-full">
            <label for="nameserver3">Name Server 3 (optional):</label>
            <x-form.input name="nameserver3" type="text" :label="__('Name Server 3:')" :placeholder="__('Enter name server 3')" wire:model="nameserver3" />
        </div>
        <div class="form-group w-full">
            <label for="nameserver4">Name Server 4 (optional):</label>
            <x-form.input name="nameserver4" type="text" :label="__('Name Server 4:')" :placeholder="__('Enter name server 4')" wire:model="nameserver4" />
        </div>
        <x-button.primary class="xl:max-w-[10%] max-w-[100px] mt-8" type="submit">Change Name Servers</x-button.primary>
    </form>
</div>