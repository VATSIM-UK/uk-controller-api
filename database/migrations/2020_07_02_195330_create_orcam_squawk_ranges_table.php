<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrcamSquawkRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orcam_squawk_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('origin');
            $table->string('first', 4)->comment('The first squawk in the range');
            $table->string('last', 4)->comment('The last squawk  in the range');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orcam_squawk_ranges');
    }
}
