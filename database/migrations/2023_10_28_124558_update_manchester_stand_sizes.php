<?php

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $file = fopen(__DIR__ . '/../data/stands/2023-size-refresh/egcc.csv', 'r');
            $egcc = Airfield::where('code', 'EGCC')->first()->id;
            while (($line = fgetcsv($file)) !== false) {
                Stand::where('airfield_id', $egcc)
                    ->where('identifier', $line[0])
                    ->update(['aerodrome_reference_code' => $line[1], 'max_aircraft_wingspan' => $line[2], 'max_aircraft_length' => $line[3]]);
            }
            fclose($file);
        });
    }
};
