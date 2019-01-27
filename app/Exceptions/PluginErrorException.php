<?php

namespace App\Exceptions;

use Exception;

/**
 * Class PluginErrorException
 * Custom exception to be passed to bugsnag to record the fact a new plugin
 * error has come in.
 */
class PluginErrorException extends Exception
{

}
