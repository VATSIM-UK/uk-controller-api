<?php

namespace App\Caster;

use App\BaseUnitTestCase;
use App\Rules\UnitDiscreteSquawkRange\FlightRules;
use App\Rules\UnitDiscreteSquawkRange\Service;
use App\Rules\UnitDiscreteSquawkRange\UnitType;
use InvalidArgumentException;

class UnitDiscreteSquawkRangeRuleCasterTest extends BaseUnitTestCase
{
    private readonly UnitDiscreteSquawkRangeRuleCaster $caster;

    public function setUp(): void
    {
        parent::setUp();
        $this->caster = new UnitDiscreteSquawkRangeRuleCaster();
    }

    public function testItReturnsUnitTypeRule()
    {
        $rule = $this->caster->get(['type' => 'UNIT_TYPE', 'rule' => 'APP']);
        $this->assertInstanceOf(UnitType::class, $rule);
        $this->assertEquals('APP', $rule->getUnitType());
    }

    public function testItReturnsFlightRulesRule()
    {
        $rule = $this->caster->get(['type' => 'FLIGHT_RULES', 'rule' => 'IFR']);
        $this->assertInstanceOf(FlightRules::class, $rule);
        $this->assertEquals('IFR', $rule->getFlightRules());
    }

    public function testItReturnsServiceRule()
    {
        $rule = $this->caster->get(['type' => 'SERVICE', 'rule' => 'BASIC']);
        $this->assertInstanceOf(Service::class, $rule);
        $this->assertEquals('BASIC', $rule->getService());
    }

    public function testItThrowsInvalidArgumentOnInvalidRuleType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->caster->get(['type' => 'WHAT', 'rule' => 'IFR']);
    }
}
