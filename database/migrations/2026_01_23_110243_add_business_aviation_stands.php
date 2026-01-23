<?php

use App\Models\Stand\StandType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private array $businessAviationStands = [
        'EGLL' => ['501', '502', '503', '504', '505', '506'],
        'EGKK' => ['230R', '230L', '231R', '231L', '232R', '232L', '233R', '233L', '234R', '234L','235R', '235L'],
    ];

    public function up(): void
    {
        $businessTypeId = DB::table('stand_types')
            ->where('key', 'BUSINESS AVIATION')
            ->value('id');

        foreach ($this->businessAviationStands as $icao => $standIdentifiers) {
            $airfieldId = DB::table('airfields')
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
            $airfieldId = DB::table('airfields')
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
