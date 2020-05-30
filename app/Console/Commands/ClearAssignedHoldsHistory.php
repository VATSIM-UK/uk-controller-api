<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearAssignedHoldsHistory extends Command
{
    protected $signature = 'holds:clean-history';

    protected $description = 'Delete any assigned hold history';

    public function handle()
    {
        DB::table('assigned_holds_history')
            ->where('assigned_at', '<', Carbon::now()->subMonths(3))
            ->delete();
    }
}
