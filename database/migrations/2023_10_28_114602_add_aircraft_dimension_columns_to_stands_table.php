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
                ->after('aerodrome_reference_code')
                ->comment('Maximum aircraft length (m)');

            $table->decimal('max_aircraft_wingspan', 5, 2)->nullable()
                ->after('max_aircraft_length')
                ->comment('Maximum aircraft wingspan (m)');

            $table->index(['airfield_id', 'max_aircraft_length', 'max_aircraft_wingspan'], 'max_aircraft_dimensions_lw');
            $table->index(['airfield_id', 'max_aircraft_wingspan', 'max_aircraft_length'], 'max_aircraft_dimensions_wl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
