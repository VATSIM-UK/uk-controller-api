
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
            $table->unsignedInteger('priority')
                ->after('terminal_id')
                ->index()
                ->default(100)
                ->comment('The priority for this terminal, lower number is higher priority');
            $table->string('destination', 4)
                ->after('priority')
                ->index()
                ->nullable()
                ->comment('Destination slug, may be partial');
            $table->string('callsign_slug')
                ->after('destination')
                ->nullable()
                ->index()
                ->comment('Partial callsign matcher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airline_terminal', function (Blueprint $table) {
            $table->dropIndex('airline_terminal_priority_index');
            $table->dropColumn('priority');
            $table->dropIndex('airline_terminal_destination_index');
            $table->dropColumn('destination');
            $table->dropIndex('airline_terminal_callsign_slug_index');
            $table->dropColumn('callsign_slug');
        });
    }
};
