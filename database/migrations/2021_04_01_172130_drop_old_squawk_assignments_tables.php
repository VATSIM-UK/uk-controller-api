<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOldSquawkAssignmentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('airfield_pairing_squawk_assignments');
        Schema::dropIfExists('ccams_squawk_assignments');
        Schema::dropIfExists('orcam_squawk_assignments');
        Schema::dropIfExists('unit_discreet_squawk_assignments');
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
