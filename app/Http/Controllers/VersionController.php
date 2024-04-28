<?php

namespace App\Http\Controllers;

use App\Exceptions\Version\ReleaseChannelNotFoundException;
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
     * Returns information about a version
     *
     * @param Version $version
     * @return JsonResponse
     */
    public function getVersion(Version $version): JsonResponse
    {
        return response()->json($this->versionService->getFullVersionDetails($version));
    }

    public function getLatestVersion(Request $request): JsonResponse
    {
        try {
            return response()->json(
                $this->versionService->getFullVersionDetails(
                    $this->versionService->getLatestVersionForReleaseChannel($request->query('channel', 'stable'))
                )
            );
        } catch (VersionNotFoundException $version) {
            return response()->json()->setStatusCode(404);
        }
    }

    public function createNewPluginVersion(Request $request): JsonResponse
    {
        if (($publishDataResponse = $this->checkPublishData($request)) !== null) {
            return $publishDataResponse;
        }

        Log::info('Received new version from GitHub', $request->json()->all());

        try {
            $this->versionService->publishNewVersionFromGithub($request->json('release.tag_name'));
        } catch (VersionAlreadyExistsException|ReleaseChannelNotFoundException $exception) {
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
