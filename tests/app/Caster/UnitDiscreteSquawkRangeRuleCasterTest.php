<?php

namespace App\Caster;

use App\BaseUnitTestCase;
use App\Rules\UnitDiscreteSquawkRange\FlightRules;
use App\Rules\UnitDiscreteSquawkRange\UnitType;
use InvalidArgumentException;

class UnitDiscreteSquawkRangeRuleCasterTest extends BaseUnitTestCase
{
    /**
     * @var UnitDiscreteSquawkRangeRuleCaster
     */
    private $caster;

    public function setUp(): void
    {
        parent::setUp();
        $this->caster = new UnitDiscreteSquawkRangeRuleCaster();
    }

    public function testItCastsToJson()
    {
        $expected = ['test' => 'foo'];
        $this->assertEquals(json_encode($expected), $this->caster->set('', '', $expected, []));
    }

    public function testItReturnsUnitTypeRule()
    {
        $rule = $this->caster->get(null, '', json_encode(['type' => 'UNIT_TYPE', 'rule' => 'APP']), []);
        $this->assertInstanceOf(UnitType::class, $rule);
        $this->assertEquals('APP', $rule->getUnitType());
    }

    public function testItReturnsFlightRulesRule()
    {
        $rule = $this->caster->get(null, '', json_encode(['type' => 'FLIGHT_RULES', 'rule' => 'IFR']), []);
        $this->assertInstanceOf(FlightRules::class, $rule);
        $this->assertEquals('IFR', $rule->getFlightRules());
    }

    public function testItThrowsInvalidArgumentOnInvalidRuleType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->caster->get(null, '', json_encode(['type' => 'WHAT', 'rule' => 'IFR']), []);
    }
}
