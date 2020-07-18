<?php

namespace App\Rules\UnitDiscreteSquawkRange;

use App\BaseUnitTestCase;

class FlightRulesTest extends BaseUnitTestCase
{
    /**
     * @var FlightRules
     */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new FlightRules('IFR');
    }

    public function testMessage()
    {
        $this->assertEquals('Flight rules do not match', $this->rule->message());
    }

    public function testItPassesFullMatch()
    {
        $this->assertTrue($this->rule->passes(null, ['rules' => 'IFR']));
    }

    public function testItPassesFirstLetterMatch()
    {
        $this->assertTrue($this->rule->passes(null, ['rules' => 'I']));
    }
    public function testItFailsOnEmpty()
    {
        $this->assertFalse($this->rule->passes(null, ['rules' => '']));
    }

    public function testItFailsOnNull()
    {
        $this->assertFalse($this->rule->passes(null, null));
    }

    public function testItFailsOnWrongRules()
    {
        $this->assertFalse($this->rule->passes(null, ['rules' => 'VFR']));
    }
}
