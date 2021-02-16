<?php
namespace App\Http\Controllers;

use App\Models\Dependency\Dependency;
use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DependencyController extends BaseController
{
    public function getDependency(int $id): JsonResponse
    {
        return response()->json(DependencyService::fetchDependencyDataById($id));
    }

    /**
     * Get all the downloadable dependency locations
     *
     * @return JsonResponse
     */
    public function getAllDependencies() : JsonResponse
    {
        $dependencies = Dependency::with('user')->get()->map(function (Dependency $dependency) {
            $updatedAt = $dependency->updated_at;
            if ($dependency->per_user) {
                if (!$dependency->user->first()) {
                    $dependency->user()->attach(
                        $dependency->id,
                        [
                            'user_id' => Auth::user()->id,
                            'updated_at' => Carbon::now(),
                        ]
                    );
                    $dependency->load('user');
                }

                $updatedAt = $dependency->user->first()->pivot->updated_at;
            } elseif (!$updatedAt) {
                $dependency->updated_at = Carbon::now();
                $dependency->save();
                $updatedAt = $dependency->updated_at;
            }

            return [
                'key' => $dependency->key,
                'uri' => sprintf('%s/dependency/%d', config('app.url'), $dependency->id),
                'local_file' => $dependency->local_file,
                'updated_at' => $updatedAt->timestamp,
            ];
        });
        return response()->json($dependencies);
    }
}
