<x-filament::page>
    @if($standRequest)
        @livewire('current-stand-request', ['standRequest' => $standRequest])
    @else
        @livewire('request-a-stand-form')
    @endif
</x-filament::page>
