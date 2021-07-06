<?php

namespace App\Console\Commands;

use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanPrenoteMessageHistory extends Command
{
    protected $signature = 'prenote-messages:clean-history';

    protected $description = 'Delete any prenote messages older than three months';

    public function handle(): int
    {
        PrenoteMessage::where(
            'created_at',
            '<',
            Carbon::now()->subMonths(3)->toDateTimeString()
        )->forceDelete();
        $this->info('Prenote message history cleaned successfully');
        return 0;
    }
}
