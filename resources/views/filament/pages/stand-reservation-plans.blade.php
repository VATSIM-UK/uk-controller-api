<x-filament-panels::page>
    <x-filament::section heading="Upload stand reservation plan" description="Plans require admin approval within 7 days before stands are allocated.">
        <form wire:submit="submitPlan" class="space-y-4">
            {{ $this->form }}

            <div class="rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-900/40">
                <p class="font-semibold">Accepted payload format</p>
                <pre class="mt-2 overflow-x-auto text-xs"><code>{
  "event_start": "2026-02-20 09:00:00",
  "event_finish": "2026-02-20 18:00:00",
  "stand_slots": [
    {
      "airport": "EGLL",
      "stand": "1L",
      "slot_reservations": [
        {
          "callsign": "SBI24",
          "cid": 1234567,
          "origin": "LFPG",
          "destination": "EGLL",
          "slotstart": "2026-02-20 09:00:00",
          "slotend": "2026-02-20 09:30:00"
        }
      ]
    }
  ]
}</code></pre>
                <p class="mt-2">Use top-level <code>event_start</code>/<code>event_finish</code> defaults and optional per-reservation <code>slotstart</code>/<code>slotend</code>. Each reservation must include <code>airport</code> directly or inherit it from its parent <code>stand_slots</code> item.</p>
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
