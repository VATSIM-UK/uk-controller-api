<?php

namespace App\Console\Commands;

use App\Exceptions\TableDoesNotExistException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimiseTables extends Command
{
    const TABLES_TO_OPTIMISE = [
        'ccams_squawk_assignments',
        'orcam_squawk_assignments',
        'airfield_pairing_squawk_assignments',
        'unit_discrete_squawk_assignments',
        'unit_discrete_squawk_range_rules',
        'squawk_assignments_history',
        'assigned_holds',
        'assigned_holds_history',
        'msl_airfield',
        'msl_tma',
        'regional_pressure_settings',
        'network_aircraft',
        'network_aircraft_fir_events',
        'stand_assignments',
        'stand_assignments_history',
    ];

    protected $signature = 'tables:optimise';

    protected $description = 'Optimise database tables';

    public function handle()
    {
        $this->info('Optimising database tables');
        foreach (self::TABLES_TO_OPTIMISE as $table) {
            if (!Schema::hasTable($table)) {
                throw new TableDoesNotExistException('Table doesnt exist to be optimised ' . $table);
            }

            DB::statement('OPTIMIZE TABLE ' . $table);
            $this->info('Optimised ' . $table);
        }
        $this->info('Finished optimising tables');
    }
}
