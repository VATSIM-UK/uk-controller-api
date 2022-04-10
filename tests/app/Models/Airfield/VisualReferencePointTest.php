<?php

namespace App\Models\Airfield;

use App\BaseUnitTestCase;
use Location\Coordinate;

class VisualReferencePointTest extends BaseUnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->vrp = new VisualReferencePoint(
            ['name' => 'M5 Avon Bridge', 'short_name' => 'M5AB', 'latitude' => 1, 'longitude' => 2]
        );
        $this->vrp->id = 5;
        $this->vrp->airfields->add(1);
        $this->vrp->airfields->add(3);
    }

    public function testItHasAnElementId()
    {
        $this->assertEquals(5, $this->vrp->elementId());
    }

    public function testItHasAnElementType()
    {
        $this->assertEquals('visual_reference_point', $this->vrp->elementType());
    }

    public function testItHasAnElementName()
    {
        $this->assertEquals('M5 Avon Bridge', $this->vrp->elementName());
    }

    public function testItHasDisplayRules()
    {
        $expected = [
            [
                'type' => 'related_airfield',
                'airfields' => [1, 3],
            ],
        ];
        $this->assertEquals($expected, $this->vrp->displayRules());
    }
}
