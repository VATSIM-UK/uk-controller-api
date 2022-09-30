<?php

namespace App\Providers;

use App\Broadcaster\PluginEventBroadcaster;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * @codeCoverageIgnore
     */
    public function boot(BroadcastManager $broadcastManager)
    {
        $broadcastManager->extend(
            'plugin-events',
            function () {
                return new PluginEventBroadcaster();
            }
        );

        Broadcast::routes(['middleware' => 'auth.api:api']);
        require_once(__DIR__ . '/../../routes/channels.php');
    }
}
