 <!-- domain Component -->
 <div x-show="activeComponent === 'domains'" x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
     @livewire('domain')
 </div>