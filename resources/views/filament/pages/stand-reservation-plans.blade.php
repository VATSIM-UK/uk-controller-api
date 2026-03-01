<x-filament-panels::page>
    <x-filament::section heading="Upload stand reservation plan" description="Plans require admin approval within 7 days before stands are allocated.">
        <form wire:submit="submitPlan" class="space-y-4">
            {{ $this->form }}

            <div class="rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-900/40">
                <p class="font-semibold">Accepted payload format</p>
                <pre class="mt-2 overflow-x-auto text-xs"><code>{
  "start": "2026-02-20 09:00:00",
  "end": "2026-02-20 18:00:00",
  "reservations": [
    {
      "airport": "EGLL",
      "stand": "1L",
      "callsign": "SBI24",
      "cid": 1234567,
      "origin": "LFPG",
      "destination": "EGLL"
    }
  ]
}</code></pre>
                <p class="mt-2">You can also use per-reservation <code>start</code>/<code>end</code>, and ensure each reservation includes <code>airport</code>.</p>
            </div>

            <x-filament::button type="submit">Submit plan</x-filament::button>
        </form>
    </x-filament::section>

    <x-filament::section heading="Review and history" description="Pending, approved, denied and expired plans are all tracked here.">
        {{ $this->table }}

        <div class="mt-6 space-y-3">
            <p class="text-sm font-medium">Recently processed plans</p>

            @livewire(
                \App\Http\Livewire\Stand\RecentlyProcessedPlansTable::class,
                [],
                key('stand-recently-processed-plans-table')
            )
        </div>
    </x-filament::section>
</x-filament-panels::page>
