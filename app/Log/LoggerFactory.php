<?php

namespace App\Log;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    /**
     * Creates a logger
     *
     * @param array $config
     * @return LoggerInterface
     */
    public function __invoke(array $config) : LoggerInterface
    {
        $monolog = new Logger($config['name']);
        $maxFiles = 7;
        $problemHandler = (new RotatingFileHandler(storage_path("logs/error.log"), $maxFiles, Logger::WARNING, false))
            ->setFormatter(new LineFormatter(null, null, true, true));

        $debugHandler = (new RotatingFileHandler(storage_path("logs/debug.log"), $maxFiles, LOGGER::DEBUG))
            ->setFormatter(new LineFormatter(null, null, true, true));

        $monolog->pushHandler($debugHandler);
        $monolog->pushHandler($problemHandler);

        return $monolog;
    }
}
