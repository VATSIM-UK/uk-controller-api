<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkRangeOwner;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRedhillSquawk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rangeOwner = DB::table('squawk_range_owner')->insertGetId([]);
        DB::table('squawk_unit')->insert(
            [
                'unit' => 'EGKR',
                'squawk_range_owner_id' => $rangeOwner,
            ]
        );
        // Create the range
        DB::table('squawk_range')->insert(
            [
                'start' => '3767',
                'stop' => '3767',
                'rules' => 'A',
                'allow_duplicate' => true,
                'squawk_range_owner_id' => $rangeOwner,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $unit = DB::table('squawk_unit')->where('unit', 'EGKR')->select('squawk_range_owner_id')->first();
        DB::table('squawk_unit')->where('unit', 'EGKR')->delete();
        DB::table('squawk_range_owner')->where('id', $unit->squawk_range_owner_id)->delete();
    }
}
