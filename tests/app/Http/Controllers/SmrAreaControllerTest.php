<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\SmrArea;
use Carbon\Carbon;

class SmrAreaControllerTest extends BaseApiTestCase
{
    public function testItReturnsFormattedActiveAreas()
    {
        SmrArea::create([
            'airfield_id' => 1,
            'coordinates' => 'COORD:ACTIVE:AREA1',
            'start_date'  => null,
            'end_date'    => null,
        ]);
        SmrArea::create([
            'airfield_id' => 1,
            'coordinates' => 'COORD:ACTIVE:AREA2',
            'start_date'  => Carbon::now()->subDay(),
            'end_date'    => Carbon::now()->addDay(),
        ]);
        SmrArea::create([
            'airfield_id' => 1,
            'coordinates' => 'COORD:INACTIVE:AREA',
            'start_date'  => null,
            'end_date'    => Carbon::now()->subDay(),
        ]);

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'smr-areas')
            ->assertOk()
            ->assertSeeText('COORD:ACTIVE:AREA1')
            ->assertSeeText('COORD:ACTIVE:AREA2')
            ->assertDontSeeText('INACTIVE')
            ->assertDontSeeText("1\nCOORD")
            ->assertDontSeeText('1COORD')
            ->assertDontSeeText("2\nCOORD")
            ->assertDontSeeText('2COORD');
    }
}
