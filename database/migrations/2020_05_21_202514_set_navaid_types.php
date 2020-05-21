<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetNavaidTypes extends Migration
{
    const NDB = [
        'BRI',
        'CDF',
        'WCO',
        'LCY',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $navaids = DB::table('navaids')->pluck('identifier');
            $fixes = [];
            foreach ($navaids as $navaid) {
                if (strlen($navaid) === 5) {
                    $fixes[] = $navaid;
                }
            }
            DB::table('navaids')->whereIn('identifier', $fixes)->update(['type' => 'FIX']);
            DB::table('navaids')->whereIn('identifier', self::NDB)->update(['type' => 'NDB']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('navaids')->update(['type' => 'VOR']);
    }
}
