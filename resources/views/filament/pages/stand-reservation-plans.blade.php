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
      "airfield": "EGLL",
      "stand": "1L",
      "callsign": "SBI24",
      "cid": 1234567,
      "origin": "LFPG",
      "destination": "EGLL"
    }
  ]
}</code></pre>
                <p class="mt-2">You can also use per-reservation <code>start</code>/<code>end</code>, and <code>airport</code> as an alias for <code>airfield</code>.</p>
            </div>

            <x-filament::button type="submit">Submit plan</x-filament::button>
        </form>
    </x-filament::section>

    <x-filament::section heading="Review and history" description="Pending, approved, denied and expired plans are all tracked here.">
        {{ $this->table }}

        <div class="mt-6 space-y-3">
            <p class="text-sm font-medium">Recently processed plans</p>
            @forelse($this->recentlyProcessedPlans() as $plan)
                <div class="rounded-lg border border-gray-200 p-3 text-sm dark:border-gray-700">
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-medium">{{ $plan->name }}</span>
                        <span class="text-xs uppercase tracking-wide">{{ $plan->status }}</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-300">Submitted by: {{ $plan->contact_email }} · Updated: {{ $plan->updated_at?->toDateTimeString() }}</p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Window: {{ $this->allocationWindowLabel($plan) }}</p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Requested stands: {{ $this->requestedStandsLabel($plan) }}</p>
                    @if($plan->status === 'approved')
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Imported reservations: {{ $plan->imported_reservations ?? 0 }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-600 dark:text-gray-300">No processed plans yet.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
