<?php

use App\Models\Aircraft\Aircraft;
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
            $table->foreignIdFor(Aircraft::class)
                ->after('remarks')
                ->comment('The matched aircraft type for this flight')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('network_aircraft', function (Blueprint $table) {
            $table->dropForeign(['aircraft_id']);
            $table->dropColumn('aircraft_id');
        });
    }
};
