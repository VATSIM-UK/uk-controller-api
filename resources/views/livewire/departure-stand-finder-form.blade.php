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
        {{ $this->form }}

        <x-filament::button type="submit" color="primary" style="margin-top: 25px">
            Find Stand
        </x-filament::button>
    </form>
</div>