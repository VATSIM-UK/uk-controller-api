<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

trait ConsidersStandRequests
{
    private function joinOtherStandRequests(Builder $query, NetworkAircraft $aircraft): Builder
    {
        if ($aircraft->cid === null) {
            return $query;
        }

        return $query->leftJoin('stand_requests as other_stand_requests', function (JoinClause $join) use ($aircraft) {
            // Prefer stands that haven't been requested by someone else
            $join->on('stands.id', '=', 'other_stand_requests.stand_id')
                ->on('other_stand_requests.user_id', '<>', $join->raw($aircraft->cid))
                ->on(
                    'other_stand_requests.requested_time',
                    '>',
                    $join->raw(
                        sprintf(
                            '\'%s\'',
                            Carbon::now()->subMinutes(
                                config('vatsim-connect.stand_request_expiry_minutes')
                            )->toDateTimeString()
                        )
                    )
                );
        });
    }
}
