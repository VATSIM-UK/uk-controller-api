<?php

use App\Models\Stand\StandType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private array $businessAviationStands = [
        'EGKK' => ['230R', '230L', '231R', '231L', '232R', '232L', '233R', '233L', '234R', '234L','235R', '235L'],
        'EGSS' => ['504', '505', '506', '507', '508'],
        'EGPH' => ['317', '317A', '316', '315'],
        'EGNT' => ['50', '51', '52', '53', '54', '60', '61', '62'],
        'EGNX' => ['21', '22', '23', '24', '31'],
        'EGBB' => ['503', '504L', '504R'],
        'EGLC' => ['13', '14'],
        'EGGW' => ['16', '17', '18', '19', '54', '56', '58', '81']
    ];

    public function up(): void
    {
        $businessTypeId = DB::table('stand_types')
            ->where('key', 'BUSINESS AVIATION')
            ->value('id');

        foreach ($this->businessAviationStands as $icao => $standIdentifiers) {
            $airfieldId = DB::table('airfield')
                ->where('code', $icao)
                ->value('id');

            DB::table('stands')
                ->where('airfield_id', $airfieldId)
                ->whereIn('identifier', $standIdentifiers)
                ->update([
                    'type_id' => $businessTypeId,
                ]);
        }
    }

    public function down(): void
    {
        $businessTypeId = DB::table('stand_types')
            ->where('key', 'BUSINESS AVIATION')
            ->value('id');

        foreach ($this->businessAviationStands as $icao => $standIdentifiers) {
            $airfieldId = DB::table('airfield')
                ->where('code', $icao)
                ->value('id');

            if (! $airfieldId) {
                continue;
            }

            DB::table('stands')
                ->where('airfield_id', $airfieldId)
                ->whereIn('identifier', $standIdentifiers)
                ->update([
                    'type_id' => null,
                ]);
        }
    }
};
