<x-filament::widget>
    <x-filament::card>
        <x-tables::header.heading>
            {{ __('widgets.roles.heading') }}
        </x-tables::header.heading>

        <x-tables::hr />

        <x-tables::table>
            <div class="prose dark:prose-invert">
                @forelse ($user->roles as $role)
                    <li>
                        {{ $role->description }}
                    </li>
                @empty
                    <li>Member</li>
                @endforelse
            </div>
        </x-tables::table>
    </x-filament::card>
</x-filament::widget>
