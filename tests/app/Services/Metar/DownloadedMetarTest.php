<?php

namespace App\Services\Metar;

use App\BaseUnitTestCase;

class DownloadedMetarTest extends BaseUnitTestCase
{
    private DownloadedMetar $metar;

    public function setUp(): void
    {
        parent::setUp();
        $this->metar = new DownloadedMetar('EGKK Q1001 CAVOK');
    }

    public function testItReturnsRaw()
    {
        $this->assertEquals('EGKK Q1001 CAVOK', $this->metar->raw());
    }

    public function testItReturnsTokenised()
    {
        $this->assertEquals(collect(['EGKK', 'Q1001', 'CAVOK']), $this->metar->tokenise());
    }

    public function testItReturnsEmptyIfNoTokens()
    {
        $emptyMetar = new DownloadedMetar('');
        $this->assertEquals(collect([]), $emptyMetar->tokenise());
    }
}
