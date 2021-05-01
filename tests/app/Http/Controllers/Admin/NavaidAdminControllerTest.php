<?php

namespace App\Http\Controllers\Admin;

use App\BaseApiTestCase;
use App\Models\Navigation\Navaid;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Providers\AuthServiceProvider;

class NavaidAdminControllerTest extends BaseApiTestCase
{
    use DatabaseTransactions;

    private string $identifier = 'NQY';
    private string $baseEndpoint = "admin/navaids";

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_DATA_ADMIN,
    ];

    public function testNavaidsCanBeListed()
    {
        $navaid = Navaid::factory()->create();

        $response = $this->makeAuthenticatedApiRequest('GET', $this->baseEndpoint);

        $response->assertStatus(200);
        $response->assertJson(['navaids' => $navaid->withCount('holds')->get()->toArray()]);
    }

    public function testNavaidCanBeRetrievedByIdentifier()
    {
        $navaid = Navaid::factory()->create();

        $response = $this->makeAuthenticatedApiRequest('GET', "{$this->baseEndpoint}/{$navaid->identifier}");

        $response->assertStatus(200);
        $response->assertJson(['navaid' => $navaid->load(['holds'])->toArray()]);
    }

    public function testValidationErrorsOnUniqueIdentifier()
    {
        Navaid::factory()->create(['identifier' => $this->identifier]);

        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 123,
            'longitude' => 456,
        ]);

        $response->assertJsonValidationErrors('identifier');
    }

    public function testValidatesIdentifierGreaterThanFive()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => 'LONGERIDENTIFIER',
            'latitude' => 123,
            'longitude' => 456,
        ]);

        $response->assertJsonValidationErrors('identifier');
    }

    public function testValidationErrorsOnBadLatitude()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 'abc',
            'longitude' => 456,
        ]);

        $response->assertJsonValidationErrors('latitude');
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testValidationErrorsOnBadLongitude()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 123,
            'longitude' => 'abc',
        ]);

        $response->assertJsonValidationErrors('longitude');
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testNavaidNotCreatedWithInvalidLatitude()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 'abc',
            'longitude' => 456,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testNavaidNotCreatedWithInvalidLongitude()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 123,
            'longitude' => 'abc',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testNavaidCanBeCreatedWithValidData()
    {
        $data = [
            'identifier' => $this->identifier,
            'latitude' => 123,
            'longitude' => 456,
        ];

        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, $data);

        $response->assertStatus(201);
        $response->assertJson(['identifier' => $this->identifier]);

        $this->assertDatabaseHas('navaids', $data);
    }

    public function testNavaidCanBeModifiedUsingSameIdentifier()
    {
        $navaid = Navaid::factory()->create(['identifier' => $this->identifier]);

        $data = [
            'identifier' => $this->identifier,
            'latitude' => 123,
            'longitude' => 456,
        ];

        $response = $this->makeAuthenticatedApiRequest('PUT', "{$this->baseEndpoint}/{$navaid->identifier}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('navaids', $data);
    }

    public function testNavaidCanBeModifiedUsingNewIdentifier()
    {
        $navaid = Navaid::factory()->create();

        $data = [
            'identifier' => 'NQZ',
            'latitude' => 123,
            'longitude' => 456,
        ];

        $response = $this->makeAuthenticatedApiRequest('PUT', "{$this->baseEndpoint}/{$navaid->identifier}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('navaids', $data);
    }

    public function testNavaidCanBeDeleted()
    {
        Navaid::factory()->create(['identifier' => $this->identifier]);

        $response = $this->makeAuthenticatedApiRequest("DELETE", "{$this->baseEndpoint}/{$this->identifier}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }
}
