<?php

namespace App\Rules\UnitDiscreteSquawkRange;

use App\BaseUnitTestCase;

class UnitTypeTest extends BaseUnitTestCase
{
    /**
     * @var UnitType
     */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new UnitType('APP');
    }

    public function testMessage()
    {
        $this->assertEquals('Unit type does not match', $this->rule->message());
    }

    public function testItPassesFullMatch()
    {
        $this->assertTrue($this->rule->passes(null, ['unit_type' => 'APP']));
    }

    public function testItPassesOnEmpty()
    {
        $this->assertTrue($this->rule->passes(null, ['unit_type' => '']));
    }

    public function testItFailsOnEmptyString()
    {
        $this->assertFalse($this->rule->passes(null, ''));
    }

    public function testItFailsOnNull()
    {
        $this->assertFalse($this->rule->passes(null, null));
    }

    public function testItFailsOnWrongRules()
    {
        $this->assertFalse($this->rule->passes(null, ['unit_type' => 'TWR']));
    }
}
