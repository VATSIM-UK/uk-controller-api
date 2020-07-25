<?php

namespace App\Allocator\Squawk;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Mockery;
use PDOException;

class AbstractSquawkAllocatorTest extends BaseFunctionalTestCase
{
    /**
     * @var Closure
     */
    private $assigner;

    public function setUp(): void
    {
        parent::setUp();
        $this->assigner = function (string $code) {
            return CcamsSquawkAssignment::create(
                [
                    'callsign' => 'BAW123',
                    'code' => $code,
                ]
            );
        };
    }

    public function testItAllocatesFirstCodeInRange()
    {
        $assignment = AbstractSquawkAllocator::assignSquawk(
            $this->assigner,
            new Collection(['0101', '0102', '0103'])
        );
        $this->assertEquals('0101', $assignment->getCode());
        $this->assertEquals('BAW123', $assignment->getCallsign());
        $this->assertEquals('CCAMS', $assignment->getType());
    }

    public function testItHandlesDuplicateEntries()
    {
        CcamsSquawkAssignment::create(
            [
                'callsign' => 'BAW456',
                'code' => '0101',
            ]
        );
        CcamsSquawkAssignment::create(
            [
                'callsign' => 'BAW789',
                'code' => '0102',
            ]
        );
        $assignment = AbstractSquawkAllocator::assignSquawk(
            $this->assigner,
            new Collection(['0101', '0102', '0103'])
        );
        $this->assertEquals('0103', $assignment->getCode());
        $this->assertEquals('BAW123', $assignment->getCallsign());
        $this->assertEquals('CCAMS', $assignment->getType());
    }

    public function testItReturnsNullOnNoCodesAvailable()
    {
        CcamsSquawkAssignment::create(
            [
                'callsign' => 'BAW456',
                'code' => '0101',
            ]
        );
        CcamsSquawkAssignment::create(
            [
                'callsign' => 'BAW789',
                'code' => '0102',
            ]
        );
        $assignment = AbstractSquawkAllocator::assignSquawk(
            $this->assigner,
            new Collection(['0101', '0102'])
        );
        $this->assertNull($assignment);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItRethrowsAnyExceptionThatIsntUniqueKeyViolation()
    {
        $this->expectException(QueryException::class);
        $mock = Mockery::mock('overload:App\\Models\\Squawk\\Ccams\\CcamsSquawkAssignment');
        $mock->shouldReceive('create')
            ->andReturnUsing(
                function () {
                    $pdoException = new PDOException();
                    $pdoException->errorInfo = [1 => 9999];
                    throw new QueryException('', [], $pdoException);
                }
            );
        AbstractSquawkAllocator::assignSquawk(
            $this->assigner,
            new Collection(['0101', '0102'])
        );
    }
}
