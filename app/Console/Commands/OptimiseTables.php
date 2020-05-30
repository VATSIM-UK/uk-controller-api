<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class OptimiseTables extends Command
{
    const TABLES_TO_OPTIMISE = [
        'squawk_allocation',
        'squawk_allocation_history',
        'assigned_holds',
        'assigned_holds_history',
        'msl_airfield',
        'msl_tma',
        'regional_pressure_settings',
    ];

    protected $signature = 'tables:optimise';

    protected $description = 'Optimise database tables';

    public function handle()
    {
        $this->info('Optimising database tables');
        foreach (self::TABLES_TO_OPTIMISE as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException('Table doesnt exist to be optimised ' . $table);
            }

            DB::statement('OPTIMIZE TABLE ' . $table);
            $this->info('Optimised ' . $table);
        }
        $this->info('Finished optimising tables');
    }
}
