<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->decimal('max_aircraft_length', 5, 2)->nullable()
                ->comment('Maximum length of aircraft in metres')
                ->change();
            $table->double('max_aircraft_wingspan', 5, 2)->nullable()
                ->comment('Maximum wingspan of aircraft in metres')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
