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
        if (hash_hmac('sha1', $request->getContent(),
                config('github.secret')) !== $request->header('X-Hub-Signature')) {
            Log::error('Invalid GitHub request signature');
            return response()->json(['message' => 'Invalid request signature'])->setStatusCode(403);
        }

        return $next($request);
    }
}
