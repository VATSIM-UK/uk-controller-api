<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE airline_stand RENAME COLUMN callsign TO full_callsign;'
        );
        DB::statement(
            'ALTER TABLE airline_terminal RENAME COLUMN callsign TO full_callsign;'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'ALTER TABLE airline_stand RENAME COLUMN full_callsign TO callsign;'
        );
        DB::statement(
            'ALTER TABLE airline_terminal RENAME COLUMN full_callsign TO callsign;'
        );
    }
};
