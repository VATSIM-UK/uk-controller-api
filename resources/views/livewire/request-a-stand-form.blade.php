<div class="space-y-6">
    @if(!$userAircraft)
    <x-filament::section>
        <div class="fi-alert fi-alert-warning">
            <div class="fi-alert-body">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-500" />
                    <p>You must be flying on the VATSIM network to be able to request a stand.</p>
                </div>
            </div>
        </div>
    </x-filament::section>
    @elseif($existingAssignment)
    <x-filament::section>
        <div class="fi-alert fi-alert-info">
            <div class="fi-alert-body">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-primary-500" />
                    <p>You cannot request a stand, as you already have a stand assigned. Stands are assigned approximately 20 minutes prior to arrival.</p>
                </div>
            </div>
        </div>
    </x-filament::section>
    @elseif(!$userAircraftType?->allocate_stands)
    <x-filament::section>
        <div class="fi-alert fi-alert-info">
            <div class="fi-alert-body">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-primary-500" />
                    <p>Stands cannot be automatically assigned to your aircraft type.</p>
                </div>
            </div>
        </div>
    </x-filament::section>
    @elseif(empty($stands))
    <x-filament::section>
        <div class="fi-alert fi-alert-warning">
            <div class="fi-alert-body">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-500" />
                    <p>There are no stands available for assignment at your destination airfield.</p>
                </div>
            </div>
        </div>
    </x-filament::section>
    @else
    <x-filament::section>
        <x-slot:heading>
            Request a Stand
        </x-slot:heading>
        <x-slot:description>
            Select a stand and time for your arrival.
        </x-slot:description>

        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit" color="primary">
                    Request Stand
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
    @endif
</div>