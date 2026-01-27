<?php

use App\Models\Stand\StandType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private array $businessAviationStands = [
        'EGLL' => ['449', '450', '451', '452', '453', '454', '455', '456', '457', '457L', '457R'],
        'EGKK' => ['150', '150R', '150L', '151', '152R', '152L', '160', '160R', '160L', '161', '234L','235R', '235L', '170', '171L', '171R', '172', '172L', '172R', '173', '174', '175', '175L', '175R', '176', '176L', '176R', '177', '178', '178L', '178R', '180', '180R'],
        'EGSS' => ['504', '505', '506', '507', '508'],
        'EGCC' => ['71', '72', '73', '80', '81', '231', '232', '233'],
        'EGPH' => ['317', '317A', '316', '315'],
        'EGNT' => ['50', '51', '52', '53', '54', '60', '61', '62'],
        'EGNX' => ['21', '22', '23', '24', '31'],
        'EGBB' => ['501', '502C', '503', '504L', '504R'],
        'EGLC' => ['15'],
        'EGGW' => ['16', '17', '18', '19', '54', '56', '58', '81'],
        'EGNH' => ['1', '2', '3'],
        'EGGP' => ['11', '12', '14', '14A', '39', '40', '41'],
        'EGHI' => ['10', '11', '12', '13', '14'],
        'EGMC' => ['16', '17', '18', '19', '20', '21']
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
