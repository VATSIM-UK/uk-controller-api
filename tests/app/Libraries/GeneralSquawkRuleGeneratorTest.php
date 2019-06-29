<?php
namespace App\Libraries;

use App\BaseUnitTestCase;
use InvalidArgumentException;

class GeneralSquawkRuleGeneratorTest extends BaseUnitTestCase
{
    /**
     * Generator
     *
     * @var GeneralSquawkRuleGenerator
     */
    private $rulesGenerator;

    public function setUp() : void
    {
        parent::setUp();
        $this->rulesGenerator = $this->app->make(GeneralSquawkRuleGenerator::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(GeneralSquawkRuleGenerator::class, $this->rulesGenerator);
    }

    public function testItRejectsUglyICAOForGeneralSquawks()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid airfield ICAOs');
        $this->rulesGenerator->generateRules(1234, 1234, 'IBK2314', false);
    }

    public function testItRejectsMalformedICAOForGeneralSquawks()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid airfield ICAOs');
        $this->rulesGenerator->generateRules("EGL", "LFP");
    }

    public function testItGeneratesTheCorrectRulesInOrder()
    {
        $expected = [
            ['departure_ident' => 'EGKK', 'arrival_ident' => 'LGKR'],
            ['departure_ident' => 'EGKK', 'arrival_ident' => 'LG'],
            ['departure_ident' => 'EG', 'arrival_ident' => 'LGKR'],
            ['departure_ident' => 'EG', 'arrival_ident' => 'LG'],
            ['departure_ident' => 'E', 'arrival_ident' => 'L'],
            ['departure_ident' => 'EGKK', 'arrival_ident' => 'L'],
            ['departure_ident' => 'E', 'arrival_ident' => 'LGKR'],
            ['departure_ident' => 'EG', 'arrival_ident' => 'L'],
            ['departure_ident' => 'E', 'arrival_ident' => 'LG'],
            ['departure_ident' => null, 'arrival_ident' => 'LGKR'],
            ['departure_ident' => 'EGKK', 'arrival_ident' => null],
            ['departure_ident' => null, 'arrival_ident' => 'LG'],
            ['departure_ident' => 'EG', 'arrival_ident' => null],
            ['departure_ident' => null, 'arrival_ident' => 'L'],
            ['departure_ident' => 'E', 'arrival_ident' => null],
            ['departure_ident' => "CCAMS", 'arrival_ident' => "CCAMS"],
        ];

        $this->assertSame($expected, $this->rulesGenerator->generateRules("EGKK", "LGKR"));
    }
}
