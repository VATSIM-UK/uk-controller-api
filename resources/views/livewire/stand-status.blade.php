<x-filament::badge :color="$standStatus['available'] ? 'success' : 'danger'" size="sm">
    {{ $standStatus['statusString'] }}
</x-filament::badge>