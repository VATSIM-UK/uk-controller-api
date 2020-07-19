<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearGroundStatusHistory extends Command
{
    protected $signature = 'ground-status:clean-history';

    protected $description = 'Delete any ground status history';

    public function handle()
    {
        DB::table('ground_status_history')
            ->where('assigned_at', '<', Carbon::now()->subMonths(3))
            ->delete();
    }
}
