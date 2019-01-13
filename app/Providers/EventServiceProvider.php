<?php

namespace App\Providers;

use App\Events\SquawkAllocationEvent;
use App\Listeners\Squawk\RecordSquawkAllocationHistory;
use Laravel\Lumen\Application;
use Laravel\Lumen\Providers\EventServiceProvider as BaseEventServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends BaseEventServiceProvider
{
    protected $listen = [
        SquawkAllocationEvent::class => [
            RecordSquawkAllocationHistory::class,
        ],
    ];

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
}
