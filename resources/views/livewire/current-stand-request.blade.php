<div>
    <h1><b>Current Stand Request</b></h1>
    <p>You have currently requested Stand <b>{{$standRequest->stand->airfieldIdentifier}}</b>. You must relinquish this
        to request a different stand.</p>
    @livewire('stand-status', ['stand' => $standRequest->stand])
    <x-filament::button wire:click="relinquish({{$standRequest->id}})" color="danger">Relinquish Stand Request
    </x-filament::button>
</div>
