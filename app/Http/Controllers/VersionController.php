<?php
namespace App\Http\Controllers;

use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Models\Version\Version;
use App\Services\VersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\Version\VersionNotFoundException;
use Illuminate\Support\Facades\Log;
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
     * @deprecated In UKCP 3.0.0 this is no longer used
     * @see getVersion
     * @param  Version $version The requested version
     * @param  VersionService $versionService A service for formatting responses.
     * @return JsonResponse
     */
    public function getVersionStatus(Version $version) : JsonResponse
    {
        // Return an appropriate response.
        return response()
            ->json($this->versionService->getVersionResponse($version->version))
            ->setStatusCode(200);
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
     * @param Version $version
     * @return JsonResponse
     */
    public function getVersion(Version $version): JsonResponse
    {
        return response()->json($this->versionService->getFullVersionDetails($version));
    }

    public function createNewPluginVersion(Request $request): JsonResponse
    {
        if (($publishDataResponse = $this->checkPublishData($request)) !== null) {
            return $publishDataResponse;
        }

        Log::info('Received new version from GitHub', $request->json()->all());

        try {
            $this->versionService->publishNewVersionFromGithub($request->json('release.tag_name'));
        } catch (VersionAlreadyExistsException $alreadyExistsException) {
            return response()->json();
        }

        return response()->json([], 201);
    }

    private function checkPublishData(Request $request): ?JsonResponse
    {
        if ($request->json('action') !== 'published') {
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
