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
}
