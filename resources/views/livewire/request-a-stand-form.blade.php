@php use Carbon\Carbon; @endphp
<div>
    @if(!$userAircraft)
        You must be flying on the VATSIM network to be able to reserve a stand.
    @elseif(empty($stands))
        There are no stands available for assignment at your destination airfield.
    @else
        <div>
            <p>Please note, requesting a stand does not guarantee that it will be assigned to you. Stands are assigned
                on a first-come first-served basis. Other pilots may still connect up on the stand,
                and the stand allocator will allocate it to another aircraft if it is the only realistic stand for their
                flight.</p>
            <p>Disconnecting from the VATSIM network for an extended period of time will cause your stand request to be
                automatically relinquished.</p>
        </div>
        <form name="stand_request_form" wire:submit.prevent="submit" method="POST">
            <b>Stand Request For:</b>
            <p style="margin-bottom: 5px;">{{$userAircraft->callsign}} at {{$userAircraft->planned_destairport}}</p>
            <label for="requested_stand"><b>Stand</b>
                <select name="requested_stand" style="margin-bottom: 5px;" wire:model="requestedStand"
                        class="block transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
                    <option value="0">--</option>
                    @foreach($stands as $id => $identifier)
                        <option value="{{$id}}">{{$identifier}}</option>
                    @endforeach
                </select>
            </label>
            @error('requestedStand') <p style="color: #FF1A1A">{{ $message }}</p> @enderror
            @if($this->stand)
                @livewire('stand-status', ['stand' => $this->stand])
            @endif
            <label for="requested_time"><b>Arrival Time (Max 24 Hours)</b>
                <input name="requested_time" style="margin-bottom: 25px;" type="datetime-local"
                       min="{{Carbon::now()->toDateTimeString('minute')}}"
                       max="{{Carbon::now()->addDay()->toDateTimeString('minute')}}"
                       wire:model="requestedTime"
                       class="block transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600">
            </label>
            @error('requestedTime') <p style="color: #FF1A1A">{{ $message }}</p> @enderror
            <x-filament::button type="submit" color="primary">Request Stand</x-filament::button>
        </form>
    @endif
</div>
