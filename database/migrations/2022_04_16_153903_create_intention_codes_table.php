<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntentionCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intention_codes', function (Blueprint $table) {
            $table->id();
            $table->json('code')->comment('Information on what code to display');
            $table->json('conditions')->comment('Conditions that make this code applicable');
            $table->unsignedInteger('priority')
                ->index()
                ->comment('The priority for the intention code, the smaller the number, the higher the priority');
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
        Schema::dropIfExists('intention_codes');
    }
}
