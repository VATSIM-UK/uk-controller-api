<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->unsignedBigInteger('max_aircraft_id_wingspan')
                ->nullable()
                ->after('max_aircraft_id')
                ->comment(
                    'The maximum possible aircraft size, in terms of wingspan, that can be accommodated on this stand.'
                );
            $table->unsignedBigInteger('max_aircraft_id_length')
                ->nullable()
                ->after('max_aircraft_id')
                ->comment(
                    'The maximum possible aircraft size, in terms of length, that can be accommodated on this stand.'
                );

            $table->foreign('max_aircraft_id_wingspan')
                ->references('id')
                ->on('aircraft')
                ->onDelete('set null');

            $table->foreign('max_aircraft_id_length')
                ->references('id')
                ->on('aircraft')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropColumn('max_aircraft_id_wingspan');
            $table->dropColumn('max_aircraft_id_length');
        });
    }
};
