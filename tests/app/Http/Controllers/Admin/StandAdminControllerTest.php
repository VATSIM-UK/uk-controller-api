<?php

namespace App\Http\Controllers\Admin;

use App\BaseApiTestCase;
use App\Models\Stand\Stand;
use App\Models\Airfield\Airfield;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StandAdminControllerTest extends BaseApiTestCase
{
    use DatabaseTransactions;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_DATA_ADMIN,
    ];

    public function testStandTypesCanBeRetrieved()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/stand-types");

        $response->assertStatus(200);
    }

    public function testStandsCanBeRetrievedForAirfield()
    {
        $airfield = Airfield::factory()->create();
        $stand = Stand::factory()->create(['airfield_id' => $airfield->id]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields/{$airfield->code}/stands");

        $response->assertStatus(200);
        $response->assertJsonStructure(['stands']);
    }

    public function testAirfieldStandCanBeRetrieved()
    {
        $airfield = Airfield::factory()->create();
        $stand = Stand::factory()->create(['airfield_id' => $airfield->id]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields/{$airfield->code}/stands/{$stand->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['stand']);
    }

    public function testAirfieldsStandsNotFoundWhenStandIsNotPartOfAirfield()
    {
        $airfield = Airfield::factory()->create();
        $stand = Stand::factory()->create();

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields/{$airfield->code}/stands/{$stand->id}");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Stand not part of airfield.']);
    }

    public function testAirfieldsWithStandsCanBeRetrieved()
    {
        $airfield = Airfield::factory()->create();

        $airfieldWithStands = Airfield::factory()->create();
        Stand::factory()->create(['airfield_id' => $airfieldWithStands->id]);


        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields");

        $response->assertStatus(200);
        $response->assertJsonFragment($airfieldWithStands->toArray(), true);
        $response->assertJsonMissing($airfield->toArray(), true);
    }

    public function testAirfieldsWithoutStandsCanBeRetrieved()
    {
        $airfield = Airfield::factory()->create();

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields?all=true");

        $response->assertStatus(200);
        $response->assertJsonFragment($airfield->toArray(), true);
    }
}