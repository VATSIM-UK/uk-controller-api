<?php

namespace App\Models\Hold;

use App\BaseFunctionalTestCase;
use App\Models\Navigation\Navaid;

class HoldModelTest extends BaseFunctionalTestCase
{
    public function testRouteKeyIsIdentifierColumn()
    {
        $model = new Navaid();
        $this->assertEquals($model->getRouteKeyName(), 'identifier');
    }
}
