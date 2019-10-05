<?php

namespace App\Models\Airfield;

use App\BaseFunctionalTestCase;

class AirfieldTest extends BaseFunctionalTestCase
{
    public function testMslAttributeReturnsNullIfNoCalculation()
    {
        $this->assertNull(Airfield::find(3)->msl_calculation);
    }

    public function testMslAttributeReturnsCalculationArray()
    {
        $expected = [
            'type' => 'direct',
            'airfield' => 'EGLL',
        ];

        $this->assertEquals($expected, Airfield::find(1)->msl_calculation);
    }
}
