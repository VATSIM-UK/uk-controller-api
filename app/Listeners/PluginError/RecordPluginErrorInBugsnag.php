<?php


namespace App\Listeners\PluginError;


use App\Events\PluginErrorReceivedEvent;
use App\Exceptions\PluginErrorException;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Support\Facades\Log;

class RecordPluginErrorInBugsnag
{
    /**
     * Handle any squawk allocation event
     *
     * @param PluginErrorReceivedEvent $errorReceivedEvent
     * @return bool
     */
    public function handle(PluginErrorReceivedEvent $errorReceivedEvent) : bool
    {
        Bugsnag::notifyException(new PluginErrorException('New plugin error reported'));
        return false;
    }
}
