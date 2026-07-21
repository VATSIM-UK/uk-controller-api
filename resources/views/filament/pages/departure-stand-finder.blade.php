<x-filament::page>
    <div class="space-y-6">
        @if(isset($result['error']))
        <x-filament::section>
            <div class="fi-alert fi-alert-warning">
                <div class="fi-alert-body">
                    <p>{{ $result['error'] }}</p>
                </div>
            </div>
        </x-filament::section>
        @elseif(isset($result['stand']))
        <x-filament::section>
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
        </x-filament::section>
        @endif

        @livewire('departure-stand-finder-form')
    </div>
</x-filament::page>