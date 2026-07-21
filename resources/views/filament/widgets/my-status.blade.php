<x-filament::widget>
    <x-filament::card>
        <div class="py-2">
            {{ __('widgets.status.heading') }}
        </div>

        @if ($controller)
        @if ($controller->controller_position_id)
        <strong class="block mt-3 text-sm font-medium text-gray-500 dark:text-gray-400">Controlling As:</strong>
        @else
        <strong class="block mt-3 text-sm font-medium text-gray-500 dark:text-gray-400">Logged In As:</strong>
        @endif
        <p class="mt-1"><strong>{{ $controller->callsign }} - {{ $controller->frequency }}</strong></p>
        @elseif($aircraft)
        <strong class="block mt-3 text-sm font-medium text-gray-500 dark:text-gray-400">Flying As:</strong>
        <p class="mt-1"><strong>{{ $aircraft->callsign }} ({{ $aircraft->planned_depairport }} - {{ $aircraft->planned_destairport }})</strong></p>

        @if($aircraft->destinationAirfield)
        <strong class="block mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Arrival Stand:</strong>
        @if($aircraft?->assignedStand)
        @if($aircraft->destinationAirfield->code === $aircraft?->assignedStand?->stand->airfield->code)
        <p class="mt-1"><strong>{{ $aircraft->assignedStand->stand->airfieldIdentifier }}</strong></p>
        @else
        <p class="mt-1">--</p>
        @endif
        @elseif($standRequest)
        <p class="mt-1">You have requested <strong>{{ $standRequest->stand->airfieldIdentifier }}</strong>.</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">This will be confirmed when you are closer to your destination.</p>
        @else
        <p class="mt-1">--</p>
        @endif
        @endif
        @else
        <p class="mt-3">Not tracked.</p>
        @endif

    </x-filament::card>
</x-filament::widget>