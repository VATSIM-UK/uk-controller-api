<?php

namespace App\Services\Metar\Parser;

use App\BaseUnitTestCase;
use App\Models\Airfield\Airfield;
use Carbon\Carbon;

class ObservationTimeParserTest extends BaseUnitTestCase
{
    private ObservationTimeParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(ObservationTimeParser::class);
        Carbon::setTestNow(Carbon::parse('2021-12-11 12:00:00')->utc());
    }

    /**
     * @dataProvider timeDataProvider
     */
    public function testItParsesData(string $timeToken, string $expectedTime)
    {
        $parsed = $this->parser->parse(new Airfield(), collect([$timeToken]));
        $this->assertCount(1, $parsed);
        $this->assertEquals(Carbon::parse($expectedTime), $parsed->offsetGet('observation_time'));
    }

    public function timeDataProvider(): array
    {
        return [
            'Day less than ten' => [
                '091600Z',
                '2021-12-09 16:00:00',
            ],
            'Day greater than ten' => [
                '151600Z',
                '2021-12-15 16:00:00',
            ],
            'Time hours less than ten' => [
                '150915Z',
                '2021-12-15 09:15:00',
            ],
            'Time hours greater than ten' => [
                '151515Z',
                '2021-12-15 15:15:00',
            ],
            'Time minutes less than ten' => [
                '151501Z',
                '2021-12-15 15:01:00',
            ],
            'Time minutes great than ten' => [
                '151519Z',
                '2021-12-15 15:19:00',
            ],
        ];
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItDoesntParseBadData(string $timeToken)
    {
        $this->assertEmpty($this->parser->parse(new Airfield(), collect([$timeToken])));
    }

    public function badDataProvider(): array
    {
        return [
            'Too short' => [
                '09600Z',
            ],
            'Too long' => [
                '0916200Z',
            ],
            'No Z on the end' => [
                '091600',
            ],
            'Extra on end' => [
                '091600ZA',
            ],
            'Extra at start' => [
                'Z091600Z',
            ],
        ];
    }
}
