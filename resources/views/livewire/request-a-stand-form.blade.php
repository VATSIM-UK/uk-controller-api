<div>
    @if(!$userAircraft)
        You must be flying on the VATSIM network to be able to request a stand.
    @elseif($existingAssignment)
        You cannot request a stand, as you already have a stand assigned. Stands are assigned approximately 20 minutes prior to arrival.
    @elseif(!$userAircraftType?->allocate_stands)
        Stands cannot be automatically assigned to your aircraft type.
    @elseif(empty($stands))
        There are no stands available for assignment at your destination airfield.
    @else
        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <x-filament::button type="submit" color="primary" style="margin-top: 25px">Request Stand
            </x-filament::button>
        </form>
    @endif
</div>
