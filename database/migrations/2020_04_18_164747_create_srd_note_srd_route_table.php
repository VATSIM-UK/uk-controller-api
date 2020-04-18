<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSrdNoteSrdRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srdnote_srdroute', function (Blueprint $table) {
            $table->unsignedBigInteger('srdnote_id');
            $table->unsignedBigInteger('srdroute_id');
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
        Schema::dropIfExists('srdnote_srdroute');
    }
}
