<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSquawkAuditHistoryIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('squawk_allocation_history', function (Blueprint $table) {
            $table->index('allocated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('squawk_allocation_history', function (Blueprint $table) {
            $table->dropIndex('squawk_allocation_history_allocated_at_index');
        });
    }
}
