<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Migration to set is_business_aviation to true on relevent aircraft
return new class extends Migration {

    private array $businessAviationAircraftCodes = [
        'C510',
        'C525',
        'C25A',
        'C25B',
        'C25C',
        'C550',
        'C551',
        'C55B',
        'C560',
        'C56X',
        'C650',
        'C680',
        'C68A',
        'C700',
        'C750',
        'CJ1',
        'CJ6',

        'GLF2',
        'GLF3',
        'GLF4',
        'GLF5',
        'GLF6',
        'GLEX',

        'F2TH',
        'F900',
        'F50',
        'FA8X',

        'E35L',
        'E50P',
        'E55P',
        'E545',
        'E550',
        'E75S',
        'E75L',

        'LJ45',
        'LJ60',
        'LJ70',
        'LJ75',
        'CL30',
        'CL35',
        'CL60',
        'GL5T',
        'GL6T',
        'GL7T',

        'HA4T',

        'SF50',
    ];

    public function up(): void
    {
        DB::table('aircraft')
            ->whereIn('icao_code', $this->businessAviationAircraftCodes)
            ->update(['is_business_aviation' => true, 'allocate_stands' => true]);
    }

    public function down(): void
    {
        DB::table('aircraft')
            ->whereIn('icao_code', $this->businessAviationAircraftCodes)
            ->update(['is_business_aviation' => false, 'allocate_stands' => false]);
    }
};
