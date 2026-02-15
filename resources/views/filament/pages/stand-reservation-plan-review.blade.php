<x-filament-panels::page>
    <div class="space-y-4">
        @forelse($this->plans as $plan)
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold">{{ $plan->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $plan->contact_email }}</p>
                        <p class="text-xs text-gray-500 mt-2">Submitted: {{ $plan->created_at?->toDateTimeString() }}</p>
                        <p class="text-xs text-gray-500">Approval due: {{ $plan->approval_due_at?->toDateTimeString() }}</p>
                    </div>
                    <div class="flex gap-2">
                        <x-filament::button color="success" wire:click="approvePlan({{ $plan->id }})">Approve</x-filament::button>
                        <x-filament::button color="danger" wire:click="denyPlan({{ $plan->id }})">Deny</x-filament::button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-600 dark:text-gray-300">No pending stand plans.</p>
        @endforelse
    </div>
</x-filament-panels::page>
