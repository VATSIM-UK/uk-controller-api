<?php
namespace App\Http\Controllers;

use App\Services\ManifestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * A controller for requesting plugin dependency manifests.
 *
 * Class DefaultController
 *
 * @package App\Http\Controllers
 */
class DependencyController extends BaseController
{
    /**
     * Get a file manifest for the plugin dependencies.
     *
     * @param  ManifestService $manifestProvider Service provider for generating manifests.
     * @return JsonResponse
     */
    public function getManifest(ManifestService $manifestProvider) : JsonResponse
    {
        // Check for essential config
        if (!env('DEPENDENCY_PUBLIC_FOLDER')) {
            Log::critical('The public dependency folder environment is not set up');
            return response()->json(
                [
                    'message' => 'Dependencies are not currently available',
                ]
            )->setStatusCode(503);
        }

        // Create the response data
        $responseData = [
            'manifest' => $manifestProvider->getManifest('public', env('DEPENDENCY_PUBLIC_FOLDER'), true)
        ];

        return response()->json(
            $responseData,
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
