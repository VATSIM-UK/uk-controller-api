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
        return response()->json(Dependency::all());
    }
}
