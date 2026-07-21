<div>
    <form wire:submit.prevent="submit">
        <x-filament::section>
            <x-slot:heading>
                Aircraft Details
            </x-slot:heading>
            <x-slot:description>
                Enter the arrival aircraft's flight details to predict stand allocations across all allocators.
            </x-slot:description>

            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button
                    type="submit"
                    color="primary"
                    wire:loading.attr="disabled"
                    wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Predict Stands</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <x-filament::loading-indicator class="h-4 w-4" />
                        Predicting...
                    </span>
                </x-filament::button>
            </div>
        </x-filament::section>
    </form>
</div>