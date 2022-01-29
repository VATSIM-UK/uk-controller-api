<?php

use App\Models\Aircraft\WakeCategory;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class StanstedA380Stands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Stand::whereIn('identifier', ['11', '12', '13', '214'])
            ->airfield('EGSS')
            ->update(['wake_category_id' => WakeCategory::where('code', 'H')->first()->id]);

        Stand::where('identifier', '6')
            ->airfield('EGSS')
            ->update(['wake_category_id' => WakeCategory::where('code', 'J')->first()->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
