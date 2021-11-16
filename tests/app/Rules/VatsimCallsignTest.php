<?php

namespace App\Rules;

use App\BaseUnitTestCase;

class VatsimCallsignTest extends BaseUnitTestCase
{
    /**
     * @var VatsimCallsign
     */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new VatsimCallsign();
    }

    public function testMessage()
    {
        $this->assertEquals('Invalid VATSIM callsign', $this->rule->message());
    }

    public function testItPassesAllLetters()
    {
        $this->assertTrue($this->rule->passes(null, 'GATWF'));
    }

    public function testItPassesLettersAndHyphen()
    {
        $this->assertTrue($this->rule->passes(null, 'G-ATWF'));
    }

    public function testItPassesUnderscore()
    {
        $this->assertTrue($this->rule->passes(null, 'LON_S_CTR'));
    }

    public function testItPassesNumbers()
    {
        $this->assertTrue($this->rule->passes(null, 'BAW123'));
    }

    public function testItPassesLowerBoundary()
    {
        $this->assertTrue($this->rule->passes(null, 'A'));
    }

    public function testItPassesUpperBoundary()
    {
        $this->assertTrue($this->rule->passes(null, '01234567890123456789'));
    }

    public function testItFailsOnEmpty()
    {
        $this->assertFalse($this->rule->passes(null, ''));
    }

    public function testItFailsOnNull()
    {
        $this->assertFalse($this->rule->passes(null, null));
    }

    public function testItFailsOnTooLong()
    {
        $this->assertFalse($this->rule->passes(null, '123456789101234567890'));
    }

    public function testItFailsOnBadCharacter()
    {
        $this->assertFalse($this->rule->passes(null, 'G-ATWF[]'));
    }
}
