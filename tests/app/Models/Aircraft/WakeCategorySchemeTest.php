<?php

namespace App\Models\Aircraft;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\DB;

class WakeCategorySchemeTest extends BaseFunctionalTestCase
{
    public function testItConvertsToArray()
    {
        // Drop some of the categories so we don't have a huge test
        DB::table('wake_categories')->whereNotIn('code', ['LM', 'H'])->delete();

        $expected = [
            'id' => 1,
            'key' => 'UK',
            'name' => 'UK',
            'categories' => [
                [
                    'id' => 3,
                    'code' => 'LM',
                    'description' => 'Lower Medium',
                    'subsequent_departure_intervals' => [],
                ],
                [
                    'id' => 5,
                    'code' => 'H',
                    'description' => 'Heavy',
                    'subsequent_departure_intervals' => [
                        [
                            'id' => 3,
                            'intermediate' => false,
                            'interval' => 120,
                            'interval_unit' => 's',
                        ],
                        [
                            'id' => 3,
                            'intermediate' => true,
                            'interval' => 180,
                            'interval_unit' => 's',
                        ],
                        [
                            'id' => 5,
                            'intermediate' => false,
                            'interval' => 4,
                            'interval_unit' => 'nm',
                        ],
                        [
                            'id' => 5,
                            'intermediate' => true,
                            'interval' => 4,
                            'interval_unit' => 'nm',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, WakeCategoryScheme::where('key', 'UK')->first()->toArray());
    }
}
