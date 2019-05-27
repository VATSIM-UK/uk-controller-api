<?php

namespace App\Providers;

use App\Events\SquawkAllocationEvent;
use App\Listeners\Squawk\RecordSquawkAllocationHistory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
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
