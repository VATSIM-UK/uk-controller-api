<x-filament::page>
    <div class="space-y-6">
        @livewire('stand-predictor-form')

        @if ($this->getCurrentPrediction())
        <x-filament::section>
            <div class="fi-alert fi-alert-success">
                <div class="fi-alert-body">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-check-circle class="h-6 w-6 text-success-500" />
                        <p class="font-semibold text-lg">Stand Predictions Ready</p>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ collect($this->getCurrentPrediction())->filter(fn ($groups) => ! empty($groups))->count() }}
                        allocator(s) returned stands
                        &middot;
                        {{ $this->getTotalStandCount() }} stand(s) found
                    </p>
                </div>
            </div>
        </x-filament::section>

        @foreach ($this->getCurrentPrediction() as $allocator => $groups)
        <x-filament::section>
            <x-slot:heading>
                {{ $this->getAllocatorLabel($allocator) }}
            </x-slot:heading>

            @if (empty($groups))
            <div class="text-gray-400 italic dark:text-gray-500 py-2">
                No stands assigned for this allocator.
            </div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400 w-16">Rank</th>
                        <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Stands</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($groups as $rank => $stands)
                    <tr wire:key="allocator-{{ $loop->parent->index }}-rank-{{ $rank }}">
                        <td class="py-2.5 pr-4 align-top">
                            <x-filament::badge :color="$this->getRankColor($rank)" size="sm">
                                {{ $rank + 1 }}
                            </x-filament::badge>
                        </td>
                        <td class="py-2.5">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($stands as $stand)
                                <x-filament::badge color="gray" size="sm">
                                    {{ $stand }}
                                </x-filament::badge>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </x-filament::section>
        @endforeach
        @endif
    </div>
</x-filament::page>