<div>
        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <x-filament::button type="submit" color="primary" style="margin-top: 25px">Predict
            </x-filament::button>
        </form>
</div>
