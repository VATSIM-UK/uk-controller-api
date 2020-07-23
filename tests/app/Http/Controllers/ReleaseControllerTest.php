<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Release\Enroute\EnrouteReleaseType;
use Illuminate\Support\Facades\DB;

class ReleaseControllerTest extends BaseApiTestCase
{
    public function testItReturnsEnrouteReleaseTypeDependency()
    {
        DB::table('enroute_release_types')->delete();
        EnrouteReleaseType::create(
            [
                'tag_string' => 'foo',
                'description' => 'foo description'
            ]
        );
        EnrouteReleaseType::create(
            [
                'tag_string' => 'bar',
                'description' => 'bar description'
            ]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'release/enroute/types')
            ->assertStatus(200)
            ->assertJson(
                [
                    [
                        'tag_string' => 'foo',
                        'description' => 'foo description'
                    ],
                    [
                        'tag_string' => 'bar',
                        'description' => 'bar description'
                    ],
                ]
            );
    }
}
