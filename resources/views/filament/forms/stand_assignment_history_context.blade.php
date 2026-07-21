<div class="space-y-4">
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Callsign</strong>
        <p class="text-sm">{{ $this->record->callsign }}</p>
    </div>
    @if (isset($this->record->context['aircraft_type']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Aircraft Type</strong>
        <p class="text-sm">{{ $this->record->context['aircraft_type'] }}</p>
    </div>
    @endif
    @if (isset($this->record->context['aircraft_departure_airfield']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Departure Airfield</strong>
        <p class="text-sm">{{ $this->record->context['aircraft_departure_airfield'] }}</p>
    </div>
    @endif
    @if (isset($this->record->context['aircraft_arrival_airfield']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Arrival Airfield</strong>
        <p class="text-sm">{{ $this->record->context['aircraft_arrival_airfield'] }}</p>
    </div>
    @endif
    @if (isset($this->record->context['flightplan_remarks']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Flightplan Remarks</strong>
        <p class="text-sm">{{ $this->record->context['flightplan_remarks'] }}</p>
    </div>
    @endif
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Stand</strong>
        <p class="text-sm">{{ $this->record->stand->airfieldIdentifier }}</p>
    </div>
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Assigned At</strong>
        <p class="text-sm">{{ $this->record->assigned_at }}</p>
    </div>
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Unassigned At</strong>
        <p class="text-sm">{{ $this->record->deleted_at ?? '--' }}</p>
    </div>
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Assignment Type</strong>
        <p class="text-sm">{{ $this->record->type }}</p>
    </div>
    {{-- The stand the user had requested at assignment time --}}
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">User Requested Stand</strong>
        @if (isset($this->record->context['requested_stand']))
        <p class="text-sm mt-1">{{ $this->record->context['requested_stand'] }}</p>
        @else
        <p class="text-sm mt-1 text-gray-400 dark:text-gray-500 italic">No stand requested.</p>
        @endif
    </div>
    {{-- All the stands that were assigned at assignment time --}}
    @if (isset($this->record->context['assigned_stands']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Assigned Stands</strong>
        @if (count($this->record->context['assigned_stands']) === 0)
        <p class="text-sm mt-1 text-gray-400 dark:text-gray-500 italic">No stands assigned.</p>
        @else
        <ol class="mt-1 text-sm list-decimal list-inside">
            @foreach ($this->record->context['assigned_stands'] as $stand)
            <li>{{ $stand }}</li>
            @endforeach
        </ol>
        @endif
    </div>
    @endif
    {{-- All the stands that were occupied at assignment time --}}
    @if (isset($this->record->context['occupied_stands']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Occupied Stands</strong>
        @if (count($this->record->context['occupied_stands']) === 0)
        <p class="text-sm mt-1 text-gray-400 dark:text-gray-500 italic">No stands occupied.</p>
        @else
        <ol class="mt-1 text-sm list-decimal list-inside">
            @foreach ($this->record->context['occupied_stands'] as $stand)
            <li>{{ $stand }}</li>
            @endforeach
        </ol>
        @endif
    </div>
    @endif
    {{-- All the stands that were requested assignment time --}}
    @if (isset($this->record->context['other_requested_stands']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Other Requested Stands</strong>
        @if (count($this->record->context['other_requested_stands']) === 0)
        <p class="text-sm mt-1 text-gray-400 dark:text-gray-500 italic">No stands requested.</p>
        @else
        <ol class="mt-1 text-sm list-decimal list-inside">
            @foreach ($this->record->context['other_requested_stands'] as $stand)
            <li>{{ $stand }}</li>
            @endforeach
        </ol>
        @endif
    </div>
    @endif
    {{-- All the stands that were unassigned as a result --}}
    @if (isset($this->record->context['removed_assignments']))
    <div>
        <strong class="text-sm font-semibold text-gray-700 dark:text-gray-300">Assignments Removed</strong>
        @if (count($this->record->context['removed_assignments']) === 0)
        <p class="text-sm mt-1 text-gray-400 dark:text-gray-500 italic">No stand assignments were removed as a result of this assignment.</p>
        @else
        <ol class="mt-1 text-sm list-decimal list-inside">
            @foreach ($this->record->context['removed_assignments'] as $assignment)
            <li>{{ $assignment['callsign'] }}</li>
            @endforeach
        </ol>
        @endif
    </div>
    @endif
</div>