<?php

namespace App\Listeners\PluginError;

use App\BaseUnitTestCase;
use App\Events\PluginErrorReceivedEvent;
use App\Models\PluginError\PluginError;

class RecordPluginErrorInBugsnagTest extends BaseUnitTestCase
{
    public function testItConstructs()
    {
        $listener = new RecordPluginErrorInBugsnag();
        $this->assertInstanceOf(RecordPluginErrorInBugsnag::class, $listener);
    }

    public function testItStopsEventPropagation()
    {
        $listener = new RecordPluginErrorInBugsnag();
        $this->assertFalse($listener->handle(new PluginErrorReceivedEvent(new PluginError)));
    }
}
