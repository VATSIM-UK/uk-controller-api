<?php

namespace App\Services\Metar;

use App\BaseUnitTestCase;

class MetarTokeniserTest extends BaseUnitTestCase
{
    private MetarTokeniser $tokeniser;

    public function setUp(): void
    {
        parent::setUp();
        $this->tokeniser = $this->app->make(MetarTokeniser::class);
    }

    public function testItTokenisesMetars()
    {
        $expected = collect(['EGKK', 'Q1001', 'CAVOK']);
        $this->assertEquals($expected, $this->tokeniser->tokenise('EGKK Q1001 CAVOK'));
    }
}
