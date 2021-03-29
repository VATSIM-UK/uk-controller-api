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
            'subsequent_departure_intervals' => [
                [
                    'id' => 1,
                    'interval' => 120,
                    'intermediate' => false,
                ],
                [
                    'id' => 1,
                    'interval' => 180,
                    'intermediate' => true,
                ]
            ],
        ];
        $this->assertEquals($expected, WakeCategory::where('code', 'LM')->first()->toArray());
    }
}
