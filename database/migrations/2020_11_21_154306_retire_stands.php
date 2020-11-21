<?php

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class RetireStands extends Migration
{
    const STANDS_TO_RETIRE = [
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::STANDS_TO_RETIRE as $airfield => $stands) {
            $airfieldId = Airfield::where('code', $airfield)->first();
            Stand::where('airfield_id', $airfieldId)->whereIn('identifier', $stands)->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There is no return.
    }
}
