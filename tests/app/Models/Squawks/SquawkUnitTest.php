<?php
namespace App\Models\Squawks;

use App\BaseFunctionalTestCase;

class SquawkUnitTest extends BaseFunctionalTestCase
{

    private $rangeOwner;
    private $range;

    public function setUp() : void
    {
        parent::setUp();
        $this->rangeOwner = SquawkRangeOwner::create();
        $this->range = Range::create(
            [
                'squawk_range_owner_id' => $this->rangeOwner->id,
                'start' => '0022',
                'stop' => '0032',
                'rules' => 'A',
                'allow_duplicate' => '0',
            ]
        );
    }

    public function testItConstructs()
    {
        $model = new SquawkUnit();
        $this->assertInstanceOf(SquawkUnit::class, $model);
    }

    public function testItCanCreateANewRange()
    {
        $model = SquawkUnit::create(['unit' => 'ABCD', 'squawk_range_owner_id' => $this->rangeOwner->id]);
        $this->assertDatabaseHas($model->getTable(), ['id' => $model->id]);
    }

    public function testItHasOneRangeOwner()
    {
        $model = SquawkUnit::create(['unit' => 'ABCD', 'squawk_range_owner_id' => $this->rangeOwner->id]);
        $this->assertEquals($this->rangeOwner->id, $model->rangeOwner->id);
    }

    public function testItHasRangesThroughItsOwner()
    {
        $model = SquawkUnit::create(['unit' => 'ABCD', 'squawk_range_owner_id' => $this->rangeOwner->id]);
        $this->assertEquals($this->range->id, $model->ranges->first()->id);
    }
}
