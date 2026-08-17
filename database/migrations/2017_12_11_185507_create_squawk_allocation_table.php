<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquawkAllocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'squawk_allocation',
            function (Blueprint $table) {
                $table->increments('id');
                $table->text('squawk', 4);
                $table->timestamp('allocated_at')->useCurrent();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('squawk_allocation');
    }
}
