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
            $table->unsignedSmallInteger('srdnote_id');
            $table->unsignedSmallInteger('srdroute_id');
            $table->timestamps();

            $table->foreign('srdnote_id')->references('id')->on('srd_notes');
            $table->foreign('srdroute_id')->references('id')->on('srd_routes');
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
