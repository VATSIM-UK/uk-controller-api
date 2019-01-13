<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuditSquawkAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('squawk_allocation', function ($table) {
            $table->unsignedInteger('allocated_by')->after('squawk')->comment('Which user allocated the squawk');
            $table->foreign('allocated_by')
                ->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('squawk_allocation', function (Blueprint $table) {
            $table->dropForeign('squawk_allocation_allocated_by_foreign');
            $table->dropColumn('allocated_by');
        });
    }
}
