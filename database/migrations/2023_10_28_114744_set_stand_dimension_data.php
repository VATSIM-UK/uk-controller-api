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
        DB::table('stands')
            ->leftJoin('aircraft as aircraft_length', 'stands.max_aircraft_id_length', '=', 'aircraft_length.id')
            ->leftJoin('aircraft as aircraft_wingspan', 'stands.max_aircraft_id_wingspan', '=', 'aircraft_wingspan.id')
            ->update([
                'stands.max_aircraft_length' => DB::raw('aircraft_length.length'),
                'stands.max_aircraft_wingspan' => DB::raw('aircraft_wingspan.wingspan'),
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
