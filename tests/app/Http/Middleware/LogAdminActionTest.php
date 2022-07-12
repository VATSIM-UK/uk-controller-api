<?php

namespace App\Http\Middleware;

use App\BaseApiTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\StandType;
use App\Providers\AuthServiceProvider;
use TestingUtils\Traits\WithSeedUsers;

class LogAdminActionTest extends BaseApiTestCase
{
    use WithSeedUsers;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_DATA_ADMIN
    ];

    public function testItConstructs()
    {
        $this->assertInstanceOf(LogAdminAction::class, $this->app->make(LogAdminAction::class));
    }

    public function testItRecordsAnAdminEvent()
    {
        $airfieldCode = Airfield::factory()->create()->code;
        $standData = [
            'identifier' => '213L',
            'terminal_id' => null,
            'type_id' => StandType::first()->id,
            'latitude' => 54.01,
            'longitude' => 4.01,
            'wake_category_id' => WakeCategory::first()->id
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            "admin/airfields/{$airfieldCode}/stands",
            $standData
        )->assertCreated();

        $this->assertDatabaseHas(
            'admin_log',
            [
                'user_id' => self::ACTIVE_USER_CID,
                'request_uri' => "/api/admin/airfields/{$airfieldCode}/stands",
                'request_body' => json_encode($standData),
            ]
        );
    }
}
