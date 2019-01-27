<?php

namespace App\Events;

use App\Models\PluginError\PluginError;

class PluginErrorReceivedEvent
{
    /**
     * @var PluginError
     */
    private $error;

    public function __construct(PluginError $error)
    {
        $this->error = $error;
    }

    /**
     * @return PluginError
     */
    public function getError(): PluginError
    {
        return $this->error;
    }
}
