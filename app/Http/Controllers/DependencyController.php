<?php
namespace App\Http\Controllers;

use App\Models\Dependency\Dependency;
use Illuminate\Http\JsonResponse;

class DependencyController extends BaseController
{
    /**
     * Get all the downloadable dependency locations
     *
     * @return JsonResponse
     */
    public function getAllDependencies() : JsonResponse
    {
        $dependencies = Dependency::all()->map(function (Dependency $dependency) {
            return [
                'key' => $dependency->key,
                'uri' => sprintf('%s/%s', config('app.url'), $dependency->uri),
                'local_file' => $dependency->local_file,
                'updated_at' => $dependency->updated_at->timestamp,
            ];
        });
        return response()->json($dependencies);
    }
}
