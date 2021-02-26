<?php

namespace App\Services;

use App\BaseUnitTestCase;
use Carbon\Carbon;

class AiracServiceTest extends BaseUnitTestCase
{
    private AiracService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AiracService::class);
    }

    public function testItGetsTheNextAiracDayFromDateIfItsToday()
    {
        // 2020/02 Airac
        Carbon::setTestNow(Carbon::parse('2021-02-25 15:00:00'));

        $this->assertEquals(
            Carbon::parse('2021-02-25 00:00:00'),
            $this->service->getNextAiracDayFromDate(Carbon::now())
        );
    }

    public function testItGetsTheNextAiracDay()
    {
        // After 2020/02 Airac
        Carbon::setTestNow(Carbon::parse('2021-03-26 15:00:00'));

        $this->assertEquals(
            Carbon::parse('2021-04-22 00:00:00'),
            $this->service->getNextAiracDayFromDate(Carbon::now())
        );
    }

    public function testItGetsTheNextAiracDayFromToday()
    {
        // After 2020/02 Airac
        Carbon::setTestNow(Carbon::parse('2021-03-26 15:00:00'));

        $this->assertEquals(
            Carbon::parse('2021-04-22 00:00:00'),
            $this->service->getNextAiracDay()
        );
    }
}
