<?php

use App\Models\Airfield;
use App\Models\Hold\HoldRestriction;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HoldRestrictionKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the old one, add a cascading one
        Schema::table('hold_restriction', function (Blueprint $table) {
            $table->dropForeign('hold_restriction_hold_id_foreign');
            $table->foreign('hold_id')->references('id')->on('hold')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the old one, add a non-cascading one
        Schema::table('hold_restriction', function (Blueprint $table) {
            $table->dropForeign('hold_restriction_hold_id_foreign');
            $table->foreign('hold_id')->references('id')->on('hold');
        });
    }
}
