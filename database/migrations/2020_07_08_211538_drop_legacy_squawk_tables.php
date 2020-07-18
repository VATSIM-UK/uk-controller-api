<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropLegacySquawkTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('squawk_allocation');
        Schema::rename('squawk_allocation_history', 'squawk_allocations_history_bak');
        Schema::dropIfExists('squawk_general');
        Schema::dropIfExists('squawk_unit');
        Schema::dropIfExists('squawk_reserved');
        Schema::dropIfExists('squawk_range');
        Schema::dropIfExists('squawk_range_owner');
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
