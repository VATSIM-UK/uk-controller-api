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

        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'smr-areas');
        $response->assertOk();

        // assertSeeText normalises whitespace in Laravel 13, so assert on the raw body.
        $content = $response->getContent();

        $this->assertStringContainsString('COORD:ACTIVE:AREA1', $content);
        $this->assertStringContainsString('COORD:ACTIVE:AREA2', $content);
        $this->assertStringNotContainsString('INACTIVE', $content);
        $this->assertStringNotContainsString("1\nCOORD", $content);
        $this->assertStringNotContainsString('1COORD', $content);
        $this->assertStringNotContainsString("2\nCOORD", $content);
        $this->assertStringNotContainsString('2COORD', $content);
    }
}
