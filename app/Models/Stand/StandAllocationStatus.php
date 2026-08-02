<?php

namespace App\Models\Stand;

enum StandAllocationStatus: string
{
    case Open = 'open';
    case ClosedForArrivals = 'closed_for_arrivals';
    case Unavailable = 'unavailable';
}
