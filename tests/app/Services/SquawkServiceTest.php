<?php
namespace App\Services;

use App\Allocator\Squawk\General\AirfieldPairingSquawkAllocator;
use App\Allocator\Squawk\General\CcamsSquawkAllocator;
use App\Allocator\Squawk\General\OrcamSquawkAllocator;
use App\Allocator\Squawk\Local\UnitDiscreteSquawkAllocator;
use App\BaseFunctionalTestCase;
use App\Exceptions\SquawkNotAllocatedException;
use App\Exceptions\SquawkNotAssignedException;
use App\Libraries\GeneralSquawkRuleGenerator;
use App\Models\Squawks\Allocation;
use App\Models\User\User;
use InvalidArgumentException;
use TestingUtils\Traits\WithSeedUsers;

class SquawkServiceTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

    /**
     * SquawkService
     *
     * @var SquawkService
     */
    private $squawkService;

    public function setUp() : void
    {
        parent::setUp();
        $this->actingAs($this->activeUser());
        $this->squawkService = $this->app->make(SquawkService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(SquawkService::class, $this->squawkService);
    }

    public function testItDeletesSquawkAssignments()
    {
        $this->assertTrue($this->squawkService->deleteSquawkAssignment('BAW123'));
    }

    public function testItDoesntDeleteNonExistantAssignments()
    {
        $this->assertFalse($this->squawkService->deleteSquawkAssignment('BAW2302'));
    }

    public function testItFindsAssignedSquawks()
    {
        $this->assertSame(
            '4723',
            $this->squawkService->getAssignedSquawk('BAW123')->squawk()
        );
    }

    public function testItMarksExistingAllocationsAsNotNew()
    {
        $this->assertFalse(
            $this->squawkService->getAssignedSquawk('BAW123')->isNewAllocation()
        );
    }


    public function testItThrowsAnExceptionIfNoSquawkAssignmentExists()
    {
        $this->expectException(SquawkNotAssignedException::class);
        $this->expectExceptionMessage('Squawk assignment not found for BAW75AZ');
        $this->squawkService->getAssignedSquawk('BAW75AZ');
    }

    public function testItGeneratesNewGeneralSquawkIfAllocationExists()
    {
        $this->assertSame(
            '1234',
            $this->squawkService->assignGeneralSquawk(
                'BAW123',
                'EGKK',
                'EGCC'
            )->squawk()
        );
    }

    public function testGeneralAllocationsAreUpdatedIfSquawkExists()
    {
        $this->assertFalse(
            $this->squawkService->assignGeneralSquawk(
                'BAW123',
                'EGKK',
                'EGCC'
            )->isNewAllocation()
        );
    }

    public function testItWontDuplicateGeneralSquawks()
    {
        $this->assertSame(
            '1234',
            $this->squawkService->assignGeneralSquawk(
                'TCX1234',
                "EGKK",
                "EGCC"
            )->squawk()
        );
        $this->assertNotEquals(
            '1234',
            $this->squawkService->assignGeneralSquawk(
                'TCX1235',
                "EGKK",
                "EGCC"
            )->squawk()
        );
    }

    public function testItAssignsGeneralSquawksFromCorrectRange()
    {
        $squawk = $this->squawkService->assignGeneralSquawk(
            'IBK2314',
            "EGKK",
            "LFPG"
        )->squawk();
        $this->assertLessThanOrEqual(3333, $squawk);
        $this->assertGreaterThanOrEqual(2222, $squawk);
    }

    public function testItAuditsWhoAssignsGeneralSquawks()
    {
        $this->squawkService->assignGeneralSquawk(
            'IBK2314',
            "EGKK",
            "LFPG"
        );
        $this->assertEquals(
            self::ACTIVE_USER_CID,
            Allocation::where('callsign', '=', 'IBK2314')->first()->allocated_by
        );
    }

    public function testItUsesRulesInOrder()
    {
        $rulesMock = $this->createMock(GeneralSquawkRuleGenerator::class);
        $rulesMock->expects($this->once())->method('generateRules')->willReturn(
            [
                ['departure_ident' => 'EGKK', 'arrival_ident' => 'EGCC'],
                ['departure_ident' => 'EGKK', 'arrival_ident' => 'LG']
            ]
        );
        $service = new SquawkService($rulesMock, new SquawkAllocationService);

        $squawk = $service->assignGeneralSquawk(
            'IBK2314',
            'EGKK',
            'EGCC'
        )->squawk();
        $this->assertSame("1234", $squawk);
    }

    public function testItWontAssignNonOctalGeneralCodes()
    {
        $digitsoctal = true;

        $squawk = $this->squawkService->assignGeneralSquawk(
            'IBK2314',
            "EGJJ",
            "EGJB"
        )->squawk();

        // Check if it is octal
        foreach (str_split($squawk) as $digit) {
            if ($digit > 7) {
                $digitsoctal = false;
            }
        }

        $this->assertTrue($digitsoctal);
    }

    public function testItWontAssignReservedCodesGeneral()
    {
        $this->assertNotEquals(
            7700,
            $this->squawkService->assignGeneralSquawk(
                'IBK2314',
                "KJFK",
                "KJFK"
            )->squawk()
        );
    }

    public function testItThrowsAnExceptionIfItCantFindAGeneralSquawk()
    {
        $rulesMock = $this->createMock(GeneralSquawkRuleGenerator::class);
        $rulesMock->expects($this->once())->method('generateRules')->willReturn([]);
        $service = new SquawkService($rulesMock, new SquawkAllocationService);

        $this->expectException(SquawkNotAllocatedException::class);
        $this->expectExceptionMessage('Unable to allocate squawk from available ranges for IBK2315');
        $service->assignGeneralSquawk('IBK2315', "ABCD", "EFGH");
    }

    public function testItThrowsAnExceptionIfTheUnitCannotBeFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit not found');
        $this->squawkService->assignLocalSquawk('BAW9AZ', 'ZZZZ', 'I');
    }

    public function testItAssignsFlightRuleSpecificLocalSquawks()
    {
        $this->assertSame('3762', $this->squawkService->assignLocalSquawk('BAW9AZ', 'EGKA', 'I')->squawk());
    }

    public function testLocalSquawksTreatsSvfrAsVfr()
    {
        $this->assertSame('3763', $this->squawkService->assignLocalSquawk('BAW9AZ', 'EGKA', 'S')->squawk());
    }

    public function testItAuditsWhoAssignsLocalSquawks()
    {
        $this->squawkService->assignLocalSquawk('BAW9AZ', 'EGKA', 'I');
        $this->assertSame(
            self::ACTIVE_USER_CID,
            Allocation::where('callsign', '=', 'BAW9AZ')->first()->allocated_by
        );
    }

    public function testItAssignsDuplicateLocalSquawksWhereAllowed()
    {
        $this->assertSame('3762', $this->squawkService->assignLocalSquawk('BAW9AZ', 'EGKA', 'I')->squawk());
        $this->assertSame('3762', $this->squawkService->assignLocalSquawk('BAW9AX', 'EGKA', 'I')->squawk());
    }

    public function testItFallsBackToNonRuleSpecificLocalSquawks()
    {
        $this->assertSame('6666', $this->squawkService->assignLocalSquawk('BAW9AX', 'EGXY', 'V')->squawk());
    }


    public function testItPrefersRuleSpecificLocalSquawksWhereAvailable()
    {
        $this->assertSame('5555', $this->squawkService->assignLocalSquawk('BAW9AX', 'EGXY', 'I')->squawk());
    }

    public function testItWillTryAnotherAvailableLocalSquawk()
    {
        $this->assertNotSame('4723', $this->squawkService->assignLocalSquawk('BAW9AX', 'EGPX', 'I')->squawk());
    }

    public function testItThrowsAnExceptionIfALocalSquawkCannotBeFound()
    {
        $this->expectException(SquawkNotAllocatedException::class);
        $this->expectExceptionMessage('Unable to allocate local squawk for BAW9AX');
        $this->squawkService->assignLocalSquawk('BAW9AX', 'EGNA', 'I');
    }

    public function testItGeneratesNewLocalSquawkIfAllocationExists()
    {
        $this->assertSame(
            '3762',
            $this->squawkService->assignLocalSquawk('BAW123', 'EGKA', 'I')->squawk()
        );
    }

    public function testLocalAllocationsAreUpdatedIfSquawkExists()
    {
        $this->assertFalse(
            $this->squawkService->assignLocalSquawk('BAW123', 'EGKA', 'I')->isNewAllocation()
        );
    }

    public function testDefaultAllocatorPreference()
    {
        $expected = [
            UnitDiscreteSquawkAllocator::class,
            AirfieldPairingSquawkAllocator::class,
            OrcamSquawkAllocator::class,
            CcamsSquawkAllocator::class,
        ];

        $this->assertEquals($expected, $this->squawkService->getAllocatorPreference());
    }
}
