<x-filament-panels::page>
    <form wire:submit="submitPlan" class="space-y-6">
        <div>
            <x-filament::input.wrapper>
                <x-filament::input
                    wire:model="name"
                    type="text"
                    placeholder="Event / Organisation"
                />
            </x-filament::input.wrapper>
            @error('name') <p class="text-danger-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-filament::input.wrapper>
                <x-filament::input
                    wire:model="contact_email"
                    type="email"
                    placeholder="Contact Email"
                />
            </x-filament::input.wrapper>
            @error('contact_email') <p class="text-danger-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-filament::input.wrapper>
                <textarea
                    wire:model="plan_json"
                    rows="16"
                    class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 dark:text-white dark:placeholder:text-gray-500"
                    placeholder='{"reservations":[{"airfield":"EGLL","stand":"1L","start":"2026-02-20 09:00:00","end":"2026-02-20 10:00:00"}]}'
                ></textarea>
            </x-filament::input.wrapper>
            @error('plan_json') <p class="text-danger-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <x-filament::button type="submit">Submit plan</x-filament::button>
        <p class="text-sm text-gray-600 dark:text-gray-300">Plans require admin approval within 7 days before stands are allocated.</p>
    </form>
</x-filament-panels::page>
