<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Migration to set is_business_aviation to true on relevent aircraft
return new class extends Migration {
    private array $businessAviationAircraftCodes = [
        'B190', 'B350', 'BE10', 'BE20', 'BE30', 'BE40', 'BE9L',
        'C208', 'C25A', 'C25B', 'C25C', 'C25M', 'C402', 'C404', 'C414', 'C441',
        'C500', 'C510', 'C525', 'C550', 'C560', 'C56X', 'C650', 'C680', 'C68A',
        'C700', 'C750',
        'CL30', 'CL35', 'CL60',
        'D220', 'D328', 'J328',
        'E35L', 'E50P', 'E545', 'E550', 'E55P',
        'EA50',
        'F2TH', 'F900', 'FA10', 'FA20', 'FA50', 'FA6X', 'FA7X', 'FA8X',
        'G159', 'G280', 'GALX', 'GLF2', 'GLF3', 'GLF4', 'GLF5', 'GLF6',
        'GA7C', 'GA8C',
        'H25A', 'H25B', 'H25C', 'HA4T',
        'HDJT',
        'LJ25', 'LJ31', 'LJ35', 'LJ40', 'LJ45', 'LJ55', 'LJ60', 'LJ75',
        'MU2',
        'P180',
        'P46T', 'PA46', 'PAY2', 'PAY3', 'PAY4', 'M600',
        'PC6T', 'PC12', 'PC24',
        'PRM1',
        'SF50',
        'K100', 'K900', 'TBM7', 'TBM8', 'TBM9',
    ];

    public function up(): void
    {
        DB::table('aircraft')
            ->whereIn('code', $this->businessAviationAircraftCodes)
            ->update(['is_business_aviation' => true, 'allocate_stands' => true]);
    }

    public function down(): void
    {
        DB::table('aircraft')
            ->whereIn('code', $this->businessAviationAircraftCodes)
            ->update(['is_business_aviation' => false, 'allocate_stands' => false]);
    }
};
