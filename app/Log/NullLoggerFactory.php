<?php

namespace App\Log;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class NullLoggerFactory
{
    /**
     * Creates a NullLogger
     *
     * @param array $config Config array
     * @return LoggerInterface
     */
    public function __invoke(array $config) : LoggerInterface
    {
        $monolog = new Logger($config['name']);
        $monolog->pushHandler(new \Monolog\Handler\NullHandler());
        return $monolog;
    }
}
