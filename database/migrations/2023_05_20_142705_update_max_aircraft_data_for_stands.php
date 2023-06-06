<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('stands')
            ->update([
                'max_aircraft_id_wingspan' => DB::raw('`max_aircraft_id`'),
                'max_aircraft_id_length' => DB::raw('`max_aircraft_id`'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
