<?php

namespace App\Models\Aircraft;

use App\BaseFunctionalTestCase;

class WakeCategoryTest extends BaseFunctionalTestCase
{
    public function testItConvertsToArray()
    {
        $expected = [
            'id' => 3,
            'code' => 'LM',
            'description' => 'Lower Medium',
            'relative_weighting' => 10,
            'subsequent_departure_intervals' => [
                [
                    'id' => 1,
                    'interval' => 120,
                    'interval_unit' => 's',
                    'intermediate' => false,
                ],
                [
                    'id' => 1,
                    'interval' => 180,
                    'interval_unit' => 's',
                    'intermediate' => true,
                ]
            ],
        ];

        $this->assertEquals($expected, WakeCategory::where('code', 'LM')->first()->toArray());
    }
}
