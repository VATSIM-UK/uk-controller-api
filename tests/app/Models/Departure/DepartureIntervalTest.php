<?php

namespace App\Models\Departure;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DepartureIntervalTest extends BaseFunctionalTestCase
{
    public function testItSerialisesToArray()
    {
        $interval = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $interval->sids()->attach([1, 2, 3]);

        $expected = [
            'id' => $interval->id,
            'interval' => $interval->interval,
            'type' => $interval->type->key,
            'expires_at' => $interval->expires_at->toDateTimeString(),
            'sids' => [
                'EGLL' => [
                    'TEST1X',
                    'TEST1Y',
                ],
                'EGBB' => [
                    'TEST1A',
                ],
            ],
        ];
        $this->assertEquals($expected, $interval->toArray());
    }
}
