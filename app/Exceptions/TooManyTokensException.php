<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when too many tokens have been
 * created for a given user.
 */
class TooManyTokensException extends Exception
{
}
