<?php

namespace App\Exceptions;

use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Exception handler class.
 *
 * Class Handler
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        OAuthServerException::class,
        StandNotFoundException::class,
        StandAlreadyAssignedException::class,
    ];
}
