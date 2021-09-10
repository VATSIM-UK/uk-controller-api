<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosedAtToStandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->index()->comment('When the stand was closed for use');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropSoftDeletes('closed_at');
        });
    }
}
