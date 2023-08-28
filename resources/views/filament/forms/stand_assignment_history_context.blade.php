<div>
    <h1><b>Callsign</b></h1>
    <p>{{ $this->record->callsign }}</p>
    <br />
    @if (isset($this->record->context['aircraft_type']))
        <h1><b>Aircraft Type</b></h1>
        <p>{{ $this->record->context['aircraft_type'] }}</p>
        <br />
    @endif
    @if (isset($this->record->context['aircraft_departure_airfield']))
        <h1><b>Departure Airfield</b></h1>
        <p>{{ $this->record->context['aircraft_departure_airfield'] }}</p>
        <br />
    @endif
    @if (isset($this->record->context['aircraft_arrival_airfield']))
        <h1><b>Arrival Airfield</b></h1>
        <p>{{ $this->record->context['aircraft_arrival_airfield'] }}</p>
        <br />
    @endif
    <h1><b>Stand</b></h1>
    <p>{{ $this->record->stand->airfieldIdentifier }}</p>
    <br />
    <h1><b>Assigned At</b></h1>
    <p>{{ $this->record->assigned_at }}</p>
    <br />
    <h1><b>Unassigned At</b></h1>
    <p>{{ $this->record->deleted_at ?? '--' }}</p>
    <br />
    <h1><b>Assignment Type</b></h1>
    <p>{{ $this->record->type }}</p>
    <br />
    {{-- All the stands that were assigned at assignment time --}}
    @if (isset($this->record->context['assigned_stands']))
        <h2><b>Assigned Stands</b></h2>
        @if (count($this->record->context['assigned_stands']) === 0)
            No stands assigned.
        @else
            <ol>
                @foreach ($this->record->context['assigned_stands'] as $stand)
                    <li>{{ $stand }}</li>
                @endforeach
            </ol>
        @endif
        <br/>
    @endif
    {{-- All the stands that were occupied at assignment time --}}
    @if (isset($this->record->context['occupied_stands']))
        <h2><b>Occupied Stands</b></h2>
        @if (count($this->record->context['occupied_stands']) === 0)
            No stands occupied.
        @else
            <ol>
                @foreach ($this->record->context['occupied_stands'] as $stand)
                    <li>{{ $stand }}</li>
                @endforeach
            </ol>
        @endif
        <br/><br/>
    @endif
    {{-- All the stands that were unassigned as a result --}}
    @if (isset($this->record->context['removed_assignments']))
        <h2><b>Assignments Removed</b></h2>
        @if (count($this->record->context['removed_assignments']) === 0)
            No stand assignments were removed as a result of this assignment.
        @else
            <ol>
                @foreach ($this->record->context['removed_assignments'] as $assignment)
                    <li>{{ $assignment['callsign'] }}</li>
                @endforeach
            </ol>
        @endif
        <br/>
    @endif
</div>
