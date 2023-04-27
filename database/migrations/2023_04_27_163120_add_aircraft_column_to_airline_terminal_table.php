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
        Schema::table('airline_terminal', function (Blueprint $table) {
            $table->unsignedBigInteger('aircraft_id')
                ->nullable()
                ->after('destination')
                ->comment('The type of aircraft this applies to');

            $table->foreign('aircraft_id')->references('id')
                ->on('aircraft')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airline_terminal', function (Blueprint $table) {
            $table->dropForeign('airline_terminal_aircraft_id_foreign');
            $table->dropColumn('aircraft_id');
        });
    }
};
