<div class="space-y-6">
    <x-filament::section>
        <x-slot:heading>
            Current Stand Request
        </x-slot:heading>

        <div class="space-y-4">
            <p>
                You have currently requested Stand <strong>{{ $standRequest->stand->airfieldIdentifier }}</strong>
                at <strong>{{ $standRequest->requested_time->format('H:i') }}Z</strong>.
            </p>

            <div>
                @include('livewire.stand-status', ['standStatus' => $this->standStatus])
            </div>

            @include('livewire.stand-booking-applicability', $this->requestedTime)

            <p class="text-sm text-gray-500 dark:text-gray-400">
                You must relinquish this to request a different stand.
            </p>
        </div>

        <x-slot:footer>
            <x-filament::button wire:click="relinquish({{ $standRequest->id }})" color="danger">
                Relinquish Stand Request
            </x-filament::button>
        </x-slot:footer>
    </x-filament::section>
</div>
