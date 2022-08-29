<x-filament::widget>
    <x-filament::card>
        <x-tables::header.heading>
            {{ __('widgets.status.heading') }}
        </x-tables::header.heading>

        <x-tables::hr />

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
                <h1>Assigned Arrival Stand:</h1>
                @if($aircraft->destinationAirfield->code === $aircraft?->assignedStand?->stand->airfield->code)
                    <p><b>{{$aircraft->assignedStand->stand->identifier}}</b></p>
                @else
                    <p>--</p>
                @endif
            @endif
        @else
            <p>Not tracked.</p>
        @endif

    </x-filament::card>
</x-filament::widget>
