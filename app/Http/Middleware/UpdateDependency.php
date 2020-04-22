<?php
namespace App\Http\Middleware;

use App\Services\DependencyService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateDependency
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $dependencies
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$dependencies)
    {
        foreach ($dependencies as $dependency) {
            DependencyService::touchDependencyByKey($dependency, Auth::user());
        }

        return $next($request);
    }
}
