<?php

use App\Models\Hold\Hold;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Hold\HoldRestriction;

class BriCdfHoldLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $bristol = Hold::where('fix', 'BRI')->firstOrfail();
        $cardiff = Hold::where('fix', 'CDF')->firstOrfail();

        $bristolRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL',
            'target' => 'EGGD',
        ];
        HoldRestriction::where('hold_id', $bristol->id)
            ->update(['restriction' => json_encode($bristolRestriction)]);

        $cardiffRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL',
            'target' => 'EGFF',
        ];
        HoldRestriction::where('hold_id', $cardiff->id)
            ->update(['restriction' => json_encode($cardiffRestriction)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $bristol = Hold::where('fix', 'BRI')->firstOrfail();
        $cardiff = Hold::where('fix', 'CDF')->firstOrfail();

        $bristolRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL+1',
            'target' => 'EGGD',
        ];
        HoldRestriction::where('hold_id', $bristol->id)
            ->update(['restriction' => json_encode($bristolRestriction)]);

        $cardiffRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL+1',
            'target' => 'EGFF',
        ];
        HoldRestriction::where('hold_id', $cardiff->id)
            ->update(['restriction' => json_encode($cardiffRestriction)]);
    }
}
