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
            $file = fopen(__DIR__ . '/../data/stands/2023-size-refresh/egkk.csv', 'r');
            $egkk = Airfield::where('code', 'EGKK')->first()->id;
            while (($line = fgetcsv($file)) !== false) {
                Stand::where('airfield_id', $egkk)
                    ->where('identifier', $line[0])
                    ->update(['max_aircraft_wingspan' => $line[1], 'max_aircraft_length' => $line[2]]);
            }
            fclose($file);
        });
    }
};
