<?php
namespace App\Models\Squawks;

use App\BaseFunctionalTestCase;

class SquawkRangeOwnerTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $model = new SquawkRangeOwner();
        $this->assertInstanceOf(SquawkRangeOwner::class, $model);
    }

    public function testItCanCreateANewOwner()
    {
        $model = SquawkRangeOwner::create();
        $this->assertDatabaseHas($model->getTable(), ['id' => $model->id]);
    }
}
