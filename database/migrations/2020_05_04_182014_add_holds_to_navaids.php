<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHoldsToNavaids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::table('navaids')->insertUsing(
                ['identifier', 'created_at'],
                DB::table('holds')
                    ->select('fix', 'created_at')
                    ->distinct()
            );

            $navaids = DB::table('navaids')
                ->select(['id', 'identifier'])
                ->get()
                ->mapWithKeys(function ($result) {
                    return [$result->identifier => $result->id];
                })
                ->toArray();

            foreach ($navaids as $identifier => $id) {
                DB::table('holds')
                    ->where('fix', $identifier)
                    ->update(['navaid_id' => $id]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            DB::table('navaids')->delete();
        });
    }
}
