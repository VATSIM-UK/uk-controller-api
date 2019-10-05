<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;

class AircraftControllerTest extends BaseApiTestCase
{
    public function testItReturnsWakeCategories()
    {
        $expected = [
            [
                'id' => WakeCategory::where('code', 'L')->firstOrFail()->id,
                'code' => 'L',
                'description' => 'Light',
            ],
            [
                'id' => WakeCategory::where('code', 'S')->firstOrFail()->id,
                'code' => 'S',
                'description' => 'Small',
            ],
            [
                'id' => WakeCategory::where('code', 'LM')->firstOrFail()->id,
                'code' => 'LM',
                'description' => 'Lower Medium',
            ],
            [
                'id' => WakeCategory::where('code', 'UM')->firstOrFail()->id,
                'code' => 'UM',
                'description' => 'Upper Medium',
            ],
            [
                'id' => WakeCategory::where('code', 'H')->firstOrFail()->id,
                'code' => 'H',
                'description' => 'Heavy',
            ],
            [
                'id' => WakeCategory::where('code', 'J')->firstOrFail()->id,
                'code' => 'J',
                'description' => 'Jumbo',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'wake-category')
            ->assertStatus(200)
            ->assertJson($expected);
    }

    public function testItGetsAircraft()
    {
        $expected = [
            [
                'id' => Aircraft::where('code', 'B738')->firstOrFail()->id,
                'code' => 'B738',
                'wake_category_id' => WakeCategory::where('code', 'LM')->firstOrFail()->id,
            ],
            [
                'id' => Aircraft::where('code', 'A333')->firstOrFail()->id,
                'code' => 'A333',
                'wake_category_id' => WakeCategory::where('code', 'H')->firstOrFail()->id,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'aircraft')
            ->assertStatus(200)
            ->assertJson($expected);
    }
}
