@php use Carbon\Carbon; @endphp
<div id="request_time">
    <p>
        Your request will expire at <b>{{$endTime->format('H:i')}}Z</b> and will be considered by
        the stand allocator from <b>{{$startTime->format('H:i')}}Z</b>.
    </p>
    <br>
    <p>
        The current time is <b>{{Carbon::now()->format('H:i')}}Z</b>.
    </p>
</div>

