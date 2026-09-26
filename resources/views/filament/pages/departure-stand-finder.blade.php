<x-filament::page>
    <div class="space-y-6">
        @if(isset($result['occupancy']))
        <x-filament::section>
            <div class="flex items-center justify-between gap-4" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                <div class="flex-1 min-w-0" style="flex: 1 1 0%; min-width: 0;">
                    @if(isset($result['error']))
                    <div class="fi-alert fi-alert-warning">
                        <div class="fi-alert-body">
                            <p>{{ $result['error'] }}</p>
                        </div>
                    </div>
                    @elseif(isset($result['stand']))
                    <div class="fi-alert fi-alert-success">
                        <div class="fi-alert-body">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-check-circle class="h-6 w-6 text-success-500" />
                                <p class="font-semibold text-lg">
                                    Stand {{ $result['stand']['identifier'] }}
                                    at {{ $result['stand']['airfield'] }}
                                </p>
                            </div>

                            @php
                            $hasDetails = $result['stand']['terminal']
                            || $result['stand']['type']
                            || $result['stand']['max_aircraft_wingspan']
                            || $result['stand']['max_aircraft_length'];
                            @endphp

                            @if($hasDetails)
                            <dl class="mt-4 grid grid-cols-2 gap-4 mb-0">
                                @if($result['stand']['terminal'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terminal</dt>
                                    <dd class="text-sm">{{ $result['stand']['terminal'] }}</dd>
                                </div>
                                @endif

                                @if($result['stand']['type'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stand Type</dt>
                                    <dd class="text-sm">{{ $result['stand']['type'] }}</dd>
                                </div>
                                @endif

                                @if($result['stand']['max_aircraft_wingspan'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Wingspan</dt>
                                    <dd class="text-sm">{{ $result['stand']['max_aircraft_wingspan'] }} m</dd>
                                </div>
                                @endif

                                @if($result['stand']['max_aircraft_length'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Length</dt>
                                    <dd class="text-sm">{{ $result['stand']['max_aircraft_length'] }} m</dd>
                                </div>
                                @endif
                            </dl>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                @php
                $occupancyColor = $result['occupancy']['percentage'] >= 85
                ? 'danger'
                : ($result['occupancy']['percentage'] >= 50 ? 'warning' : 'success');
                @endphp
                <div class="shrink-0 text-right" style="flex-shrink: 0; text-align: right;">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupancy</p>
                    <x-filament::badge :color="$occupancyColor">
                        {{ $result['occupancy']['occupied'] }}/{{ $result['occupancy']['total'] }} ({{ $result['occupancy']['percentage'] }}%)
                    </x-filament::badge>
                </div>
            </div>
        </x-filament::section>
        @endif

        @livewire('departure-stand-finder-form')
    </div>
</x-filament::page>
