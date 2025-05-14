<?php

namespace Tests\Feature\app\Models;

use App\BaseFunctionalTestCase;
use App\Models\SmrArea;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SmrAreaTest extends BaseFunctionalTestCase
{
    private function createTestSmrArea(): SmrArea
    {
        return SmrArea::create([
            'airfield_id' => 1,
            'coordinates' => '',
            'start_date'  => null,
            'end_date'    => null,
        ]);
    }

    private function checkCases(Builder $query, array $cases)
    {
        $area = $this->createTestSmrArea();
        foreach ($cases as [$stt, $end, $expected]) {
            $area->start_date = $stt ? Carbon::now()->addDays($stt) : null;
            $area->end_date   = $end ? Carbon::now()->addDays($end) : null;
            $area->save();

            $this->assertEquals(
                $expected, $query->get()->contains($area),
                sprintf("expected [%d, %d] -> %d", $stt, $end, $expected),
            );
        }
    }

    public function testItFindsExpiredAreas()
    {
        $this->checkCases(SmrArea::expired(), [
            [null, null, false],
            [null,   -1, true],
            [null,    2, false],
            [  -2, null, false],
            [  -2,   -1, true],
            [  -2,    2, false],
            [   1, null, false],
            [   1,    2, false],
        ]);
    }

    public function testItFindsActiveAreas()
    {
        $this->checkCases(SmrArea::active(), [
            [null, null, true],
            [null,   -1, false],
            [null,    2, true],
            [  -2, null, true],
            [  -2,   -1, false],
            [  -2,    2, true],
            [   1, null, false],
            [   1,    2, false],
        ]);
    }

    public function testItIgnoresDeletedAreas()
    {
        $area = $this->createTestSmrArea();
        $area->delete();
        $this->assertFalse(SmrArea::active()->get()->contains($area));
        $this->assertFalse(SmrArea::expired()->get()->contains($area));
    }
}
