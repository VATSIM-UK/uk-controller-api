<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GithubAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $signatureParts = explode('=', $request->header('X-Hub-Signature'));
        if (count($signatureParts) != 2) {
            Log::error('Invalid GitHub request signature format');
            return response()->json(['message' => 'Invalid request signature format'])->setStatusCode(400);
        }

        if (hash_hmac($signatureParts[0], $request->getContent(),
                config('github.secret')) !== $signatureParts[1]) {
            Log::error('Invalid GitHub request signature');
            return response()->json(['message' => 'Invalid request signature'])->setStatusCode(403);
        }

        return $next($request);
    }
}
