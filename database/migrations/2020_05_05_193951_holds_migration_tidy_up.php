<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HoldsMigrationTidyUp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->foreign('navaid_id')->references('id')->on('navaids')->onDelete('cascade');
            $table->dropColumn('fix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->dropForeign('holds_navaid_id_foreign');
            $table->string('fix')->after('id')->comment('The fix for the hold');
        });

        $navaids = DB::table('navaids')
            ->select(['id', 'identifier'])
            ->get()
            ->mapWithKeys(function ($result) {
                return [
                    $result->id => $result->identifier
                ];
            });

        foreach ($navaids as $id => $navaid) {
            DB::table('holds')
                ->where('navaid_id', $id)
                ->update(['fix' => $navaid]);
        }
    }
}
