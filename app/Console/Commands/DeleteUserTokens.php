<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserTokenService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Command that deletes all user tokens
 */
class DeleteUserTokens extends Command
{
    protected $signature = 'tokens:delete-user {vatsim_cid}';

    protected $description = 'Remove all access tokens related to a user';

    /**
     * Run the command
     *
     * @param UserTokenService $userTokenService
     * @return integer
     */
    public function handle(UserTokenService $userTokenService)
    {
        if (!ctype_digit($this->argument('vatsim_cid'))) {
            $this->error('Invalid VATSIM CID');
            return 1;
        }

        try {
            $userTokenService->deleteAllForUser($this->argument('vatsim_cid'));
            $this->info('All access tokens deleted for user ' . $this->argument('vatsim_cid'));
            return 0;
        } catch (ModelNotFoundException $e) {
            // Nothing to catch
        }

        $this->error('User ' . $this->argument('vatsim_cid') . ' not found');
        return 2;
    }
}
