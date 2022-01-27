<?php

namespace App\Models\Hold;

use Illuminate\Database\Eloquent\Relations\Pivot;

class NavaidNetworkAircraft extends Pivot
{
    protected $dates = [
        'entered_at',
    ];
}
