<?php

namespace App\Console\Commands;

use App\Exceptions\TableDoesNotExistException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimiseTables extends Command
{
    const TABLES_TO_OPTIMISE = [
        'squawk_assignments_history',
        'assigned_holds',
        'assigned_holds_history',
        'msl_airfield',
        'msl_tma',
        'regional_pressure_settings',
        'network_aircraft',
        'stand_assignments',
        'stand_assignments_history',
        'aircraft_stand',
        'stand_assignments',
        'plugin_events',
        'departure_release_requests',
        'prenote_messages',
        'missed_approach_notifications',
        'network_controller_positions',
        'failed_jobs',
        'navaid_network_aircraft',
        'monitored_scheduled_task_log_items',
        'acars_messages',
    ];

    protected $signature = 'tables:optimise';

    protected $description = 'Optimise database tables';

    /**
     * @codeCoverageIgnore
     */
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
