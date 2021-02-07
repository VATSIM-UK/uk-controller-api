<?php
namespace App\Http\Controllers;

use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Services\VersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\Version\VersionNotFoundException;
use Illuminate\Support\Facades\Validator;

/**
 * Controller for handling plugin version checks.
 *
 * @package App\Http\Controllers
 */
class VersionController extends BaseController
{
    /**
     * Service for performing operations regarding Versions
     *
     * @var VersionService
     */
    private $versionService;

    /**
     * Constructor
     *
     * @param VersionService $versionService
     */
    public function __construct(VersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    /**
     * Checks whether the plugin is using the correct version
     * and returns this information.
     *
     * @param  string $version The requested version
     * @param  VersionService $versionService A service for formatting responses.
     * @return JsonResponse
     */
    public function getVersionStatus(string $version) : JsonResponse
    {
        // Return an appropriate response.
        return response()
            ->json($this->versionService->getVersionResponse($version))
            ->setStatusCode(200);
    }

    /**
     * Creates or updates a version and sets its allowable status
     *
     * @param string $version
     * @return JsonResponse
     */
    public function createOrUpdateVersion(Request $request, string $version) : JsonResponse
    {
        $check = $this->checkForSuppliedData(
            $request,
            [
                'allowed' => 'required|boolean',
            ]
        );

        if ($check) {
            return $check;
        }

        $created = $this->versionService->createOrUpdateVersion($version, $request->json('allowed'));
        
        return response()->json()->setStatusCode($created ? 201 : 204);
    }

    /**
     * Returns a collection of all the versions.
     *
     * @return JsonResponse
     */
    public function getAllVersions() : JsonResponse
    {
        return response()
            ->json($this->versionService->getAllVersions())
            ->setStatusCode(200);
    }

    /**
     * Returns information about a version
     *
     * @string $version The version string
     * @return JsonResponse
     */
    public function getVersion(string $version): JsonResponse
    {
        try {
            return response()
                ->json($this->versionService->getVersion($version))
                ->setStatusCode(200);
        } catch (VersionNotFoundException $e) {
            return response()->json()->setStatusCode(404);
        }
    }

    public function createNewPluginVersion(Request $request): JsonResponse
    {
        if (($publishDataResponse = $this->checkPublishData($request)) !== null) {
            return $publishDataResponse;
        }

        try {
            $this->versionService->publishNewVersionFromGithub($request->json('release.tag_name'));
        } catch (VersionAlreadyExistsException $alreadyExistsException)
        {
            return response()->json();
        }

        return response()->json(201);
    }

    private function checkPublishData(Request $request): ?JsonResponse
    {
        if ($request->json('action') !== 'published')
        {
            return response()->json();
        }

        $validator = Validator::make(
            $request->json()->all(),
            [
                'release' => 'required|array',
                'release.tag_name' => 'required|string',
            ]
        );

        return $validator->fails() ? response()->json([], 400) : null;
    }
}
