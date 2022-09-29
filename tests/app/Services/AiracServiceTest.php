<?php

namespace App\Services;

use App\BaseUnitTestCase;
use Carbon\Carbon;

class AiracServiceTest extends BaseUnitTestCase
{
    public function testItGetsThePreviousAiracDayIfItsToday()
    {
        // 2020/02 Airac
        Carbon::setTestNow(Carbon::parse('2021-02-25 15:00:00'));

        $this->assertEquals(
            Carbon::parse('2021-02-25 00:00:00'),
            AiracService::getPreviousAiracDay()
        );
    }

    public function testItGetsThePreviousAiracDay()
    {
        // After 2020/02 Airac
        Carbon::setTestNow(Carbon::parse('2021-03-26 15:00:00'));

        $this->assertEquals(
            Carbon::parse('2021-03-25 00:00:00'),
            AiracService::getPreviousAiracDay()
        );
    }

    public function testItHasAValidBaseAiracDate()
    {
        $this->assertEquals(
            Carbon::parse('2021-01-28 00:00:00'),
            AiracService::getBaseAiracDate()
        );
    }

    /**
     * @dataProvider airacDataProvider
     */
    public function testItGeneratesTheCurrentAirac(string $currentDate, string $expected)
    {
        Carbon::setTestNow(Carbon::parse($currentDate));

        $this->assertEquals($expected, AiracService::getCurrentAirac());
    }

    private function airacDataProvider(): array
    {
        return [
            'Before 2201' => ['2022-01-26 00:00:00', '2113'],
            'Airac 2201' => ['2022-01-27 00:00:00', '2201'],
            'End of 2201' => ['2022-02-23 00:00:00', '2201'],
            'Airac 2202' => ['2022-02-24 00:00:00', '2202'],
            'Airac 2209' => ['2022-09-20 00:00:00', '2209'],
            'Airac 2211' => ['2022-11-04 00:00:00', '2211'],
            'Airac 2213' => ['2022-12-30 00:00:00', '2213'],
            '2213 in 2023' => ['2023-01-04 00:00:00', '2213'],
            'Airac 2608' => ['2026-08-15 00:00:00', '2608'],
        ];
    }
}
