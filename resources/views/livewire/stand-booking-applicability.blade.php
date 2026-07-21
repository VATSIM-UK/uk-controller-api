@php use Carbon\Carbon; @endphp
<div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
    <p>
        Your request will expire at <strong>{{ $endTime->format('H:i') }}Z</strong> and will be considered by
        the stand allocator from <strong>{{ $startTime->format('H:i') }}Z</strong>.
    </p>
    <p>
        The current time is <strong>{{ Carbon::now()->format('H:i') }}Z</strong>.
    </p>
</div>