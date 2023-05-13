<div>
    <div id="current_stand" style="margin-bottom: 10px">
        <h1><b>Current Stand Request</b></h1>
        <p>You have currently requested Stand <b>{{$standRequest->stand->airfieldIdentifier}}</b> at
            <b>{{$standRequest->requested_time->format('H:i')}}Z</b>.</p>

        @include('livewire.stand-booking-applicability', $this->requestedTime)

        <p>You must relinquish this to request a different stand.</p>
    </div>
    <div id="stand_status_container" style="margin-bottom: 10px">
        @include('livewire.stand-status', ['standStatus' => $this->standStatus])
    </div>
    <x-filament::button wire:click="relinquish({{$standRequest->id}})" color="danger">Relinquish Stand Request
    </x-filament::button>
</div>
