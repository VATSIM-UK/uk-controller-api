<?php
namespace App\Models\Squawks;

use App\BaseFunctionalTestCase;
use App\Libraries\SquawkValidator;

class SquawkRangeTest extends BaseFunctionalTestCase
{
    protected $range = [];

    public function setUp() : void
    {
        parent::setUp();
        $squawkRangeOwner = SquawkRangeOwner::create();
        $this->range = [
            'squawk_range_owner_id' => $squawkRangeOwner->id,
            'start' => '5050',
            'stop' => '5077',
            'rules' => 'A',
            'allow_duplicate' => 0,
        ];
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(SquawkRangeOwner::class, $this->app->make(SquawkRangeOwner::class));
    }

    public function testItCanCreateANewRange()
    {
        Range::create($this->range);
        $this->assertDatabaseHas("squawk_range", $this->range);
    }

    public function testItGetsCorrectRangeOfRange()
    {
        $range = Range::create($this->range);

        $expected_range = $this->range['stop'] - $this->range['start'] + 1;

        $this->assertEquals($expected_range, $range->number_of_possibilities);
    }

    public function testItGeneratesSquawks()
    {
        $rangeSingle = $this->range;
        $rangeSingle['stop'] = $rangeSingle['start'];

        $range = Range::create($rangeSingle);
        $squawk = $range->random_squawk;

        $this->assertEquals($this->range['start'], $squawk);
    }

    public function testItGeneratesSquawksWithinRange()
    {
        $range = Range::create($this->range);

        $squawk = $range->random_squawk;

        // Test it is valid
        $this->assertTrue(SquawkValidator::isValidSquawk($squawk));

        // Test it is within the specified range
        $this->assertTrue(
            ($this->range['start'] <= $squawk) &&
            ($squawk <= $this->range['stop'])
        );
    }

    public function testItHandlesSquawksWithThreeLeadingZero()
    {
        $this->range['start'] = '0001';
        $this->range['stop'] = '0001';
        $range = Range::create($this->range);

        $this->assertTrue(SquawkValidator::isValidSquawk($range->random_squawk));
        $this->assertSame('0001', $range->random_squawk);
    }

    public function testItHandlesSquawksWithTwoLeadingZero()
    {
        $this->range['start'] = '0045';
        $this->range['stop'] = '0045';
        $range = Range::create($this->range);

        $this->assertTrue(SquawkValidator::isValidSquawk($range->random_squawk));
        $this->assertSame('0045', $range->random_squawk);
    }

    public function testItHandlesSquawksWithOneLeadingZero()
    {
        $this->range['start'] = '0222';
        $this->range['stop'] = '0222';
        $range = Range::create($this->range);

        $this->assertTrue(SquawkValidator::isValidSquawk($range->random_squawk));
        $this->assertSame('0222', $range->random_squawk);
    }

    public function testItHasAnOwner()
    {
        $this->assertInstanceOf(SquawkRangeOwner::class, Range::find(1)->squawkRangeOwner);
    }
}
