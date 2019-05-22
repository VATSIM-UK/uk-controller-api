<?php
namespace App\Models\Squawks;

use App\BaseFunctionalTestCase;

class SquawkGeneralTest extends BaseFunctionalTestCase
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
        $model = new SquawkGeneral();
        $this->assertInstanceOf(SquawkGeneral::class, $model);
    }

    public function testItCanCreateANewRange()
    {
        $model = SquawkGeneral::create(
            [
                'departure_ident' => 'EGLF',
                'arrival_ident' => 'EGNX',
                'squawk_range_owner_id' => $this->rangeOwner->id
            ]
        );
        $this->seeInDatabase($model->getTable(), ['id' => $model->id]);
    }

    public function testItHasOneRangeOwner()
    {
        $model = SquawkGeneral::create(
            [
                'departure_ident' => 'EGLF',
                'arrival_ident' => 'EGNX',
                'squawk_range_owner_id' => $this->rangeOwner->id
            ]
        );
        $this->assertEquals($this->rangeOwner->id, $model->rangeOwner->id);
    }

    public function testItHasRangesThroughItsOwner()
    {
        $model = SquawkGeneral::create(
            [
                'departure_ident' => 'EGLF',
                'arrival_ident' => 'EGNX',
                'squawk_range_owner_id' => $this->rangeOwner->id
            ]
        );
        $this->assertEquals($this->range->id, $model->ranges->first()->id);
    }
}
