<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddAircraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the categories ready
        $wakeCategories = WakeCategory::all();
        $categoryArray = [];
        $wakeCategories->each(function (WakeCategory $category) use (&$categoryArray) {
            $categoryArray[$category->code] = $category->id;
        });

        Aircraft::insert($this->getAircraftData($categoryArray));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Aircraft::truncate();
    }

    private function getAircraftData(array $wakeCategories)
    {
        return [
            [
                'code' => 'B703',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B752',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B753',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DC85',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DC86',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DC87',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'IL62',
                'wake_category_id' => $wakeCategories['UM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A318',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A319',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A19N',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A320',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A20N',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A321',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'A21N',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'RJ85',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'RJ70',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'RJ1H',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B461',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B462',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B463',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'GLEX',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B712',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B721',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B722',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B731',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B732',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B733',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B734',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B735',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B736',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B737',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B738',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'B739',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'E190',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'E195',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'F100',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'GLF5',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'C130',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'L188',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'MD81',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'MD82',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'MD83',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'MD87',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'MD88',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'MD90',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'T134',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'T154',
                'wake_category_id' => $wakeCategories['LM'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'AT45',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'AT46',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'AT72',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'AT73',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'AT75',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'AT76',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'ATP',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'CL30',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'CL60',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'CRJ1',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'CRJ2',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'CRJ7',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'FA50',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DH8A',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DH8B',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DH8C',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'DH8D',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'E135',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'E145',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'E170',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'F27',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'F28',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'F50',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'F70',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'GLF4',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'SB20',
                'wake_category_id' => $wakeCategories['S'],
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
