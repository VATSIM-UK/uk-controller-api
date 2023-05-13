@php use Carbon\Carbon; @endphp
<div id="request_time">
    <p style="color: green">
        Your request will expire at <b>{{$endTime->format('H:i')}}Z</b> and will be considered by
        the stand allocator from <b>{{$startTime->format('H:i')}}Z</b>.
    </p>
    <p>
        The current time is <b>{{Carbon::now()->format('H:i')}}Z</b>.
    </p>
</div>

