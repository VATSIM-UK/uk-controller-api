<?php

use App\Models\Airline\Airline;
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
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->foreignIdFor(Airline::class)
                ->nullable()
                ->constrained()
                ->after('aircraft_id')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->dropForeign(['airline_id']);
            $table->dropColumn('airline_id');
        });
    }
};
