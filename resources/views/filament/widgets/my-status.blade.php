<x-filament::widget>
    <x-filament::card>
        <div class="py-2">
            {{ __('widgets.status.heading') }}
        </div>

        @if ($controller)
            @if ($controller->controller_position_id)
                <h1>Controlling As:</h1>
            @else
                <h1>Logged In As:</h1>
            @endif
            <p><b>{{$controller->callsign}} - {{$controller->frequency}}</b></p>
        @elseif($aircraft)
            <h1>Flying As:</h1>
            <p><b>{{$aircraft->callsign}} ({{$aircraft->planned_depairport}} - {{$aircraft->planned_destairport}})</b></p>

            @if($aircraft->destinationAirfield)
                @if($aircraft?->assignedStand)
                    <h1>Assigned Arrival Stand:</h1>
                    @if($aircraft->destinationAirfield->code === $aircraft?->assignedStand?->stand->airfield->code)
                        <p><b>{{$aircraft->assignedStand->stand->airfieldIdentifier}}</b></p>
                    @else
                        <p>--</p>
                    @endif
                @elseif($standRequest)
                        <h1>Assigned Arrival Stand:</h1>
                        <p>You have requested <b>{{$standRequest->stand->airfieldIdentifier}}</b>.</p>
                        <p>This will be confirmed when you are closer to your destination.</p>
                @else
                    <h1>Assigned Arrival Stand:</h1>
                    <p>--</p>
                @endif
            @endif
        @else
            <p>Not tracked.</p>
        @endif

    </x-filament::card>
</x-filament::widget>
