<?php

namespace App\Providers;

use App\Broadcaster\PluginEventBroadcaster;
use BeyondCode\LaravelWebSockets\Concerns\PushesToPusher;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Pusher\Pusher;

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
                $config = config('broadcasting.connections.pusher');

                return new PluginEventBroadcaster(
                    new PusherBroadcaster(
                        new Pusher(
                            $config['key'],
                            $config['secret'],
                            $config['app_id'],
                            $config['options'] ?? []
                        )
                    )
                );
            }
        );

        Broadcast::routes(['middleware' => 'auth.api:api']);
        require_once(__DIR__ . '/../../routes/channels.php');
    }
}
