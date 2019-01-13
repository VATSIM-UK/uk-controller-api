<?php

namespace App\Console\Commands;

use Laravel\Passport\Token;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

/**
 * Command that deletes all expired access tokens
 */
class DeleteExpiredTokens extends Command
{
    protected $signature = 'tokens:delete-expired';

    protected $description = 'Remove expired access tokens from the database';

    public function handle()
    {
        $tokens = Token::where('expires_at', '<', Carbon::now());
        $count = $tokens->count();
        $tokens->delete();
        $this->info('Deleted ' . $count . ' expired access tokens');
    }
}
