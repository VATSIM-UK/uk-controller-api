<?php

namespace App\Events;

use App\BaseUnitTestCase;
use App\Models\PluginError\PluginError;

class PluginErrorReceivedEventTest extends BaseUnitTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(PluginErrorReceivedEvent::class, new PluginErrorReceivedEvent(new PluginError));
    }

    public function testItCanReturnTheError()
    {
        $error = new PluginError;
        $event = new PluginErrorReceivedEvent($error);
        $this->assertEquals($error, $event->getError());
    }
}
