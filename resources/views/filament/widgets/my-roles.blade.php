<x-filament::widget>
    <x-filament::card>
        <div class="py-2">
            {{ __('widgets.roles.heading') }}
        </div>

        
        <div class="prose dark:prose-invert">
            @forelse ($user->roles as $role)
                <li>
                    {{ $role->description }}
                </li>
            @empty
                <li>Member</li>
            @endforelse
        </div>
    </x-filament::card>
</x-filament::widget>
