<div>
    @if($prefiledFlightplan)
    <div class="fi-alert fi-alert-info mb-4">
        <div class="fi-alert-body">
            <div class="flex items-center gap-2">
                <x-heroicon-o-information-circle class="h-5 w-5 text-primary-500" />
                <p>
                    <strong>Flight plan loaded from VATSIM:</strong>
                    {{ $prefiledFlightplan['callsign'] }}
                    {{ $prefiledFlightplan['departure'] }} &rarr;
                    {{ $prefiledFlightplan['arrival'] }}
                    ({{ $prefiledFlightplan['aircraft_short'] }})
                </p>
            </div>
        </div>
    </div>
    @endif

    <form wire:submit.prevent="submit">
        <x-filament::section>
            <x-slot:heading>
                Aircraft Details
            </x-slot:heading>
            <x-slot:description>
                Enter the aircraft details to find a suitable departure stand.
            </x-slot:description>

            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit" color="primary">
                    Find Stand
                </x-filament::button>
            </div>
        </x-filament::section>
    </form>
</div>
