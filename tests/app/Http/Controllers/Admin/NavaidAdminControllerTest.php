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
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W004.33.19.000'
        ]);

        $response->assertJsonValidationErrors('identifier');
    }

    public function testValidatesIdentifierGreaterThanFive()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => 'LONGERIDENTIFIER',
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W004.33.19.000'
        ]);

        $response->assertJsonValidationErrors('identifier');
    }

    public function testValidationErrorsOnBadlyFormattedLatitudeString()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 'N91.26.33.160',
            'longitude' => 'W003.33.19.000'
        ]);

        $response->assertJsonValidationErrors('latitude');
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testValidationErrorsOnBadlyFormattedLongitudeString()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 'N091.26.33.160',
            'longitude' => 'W03.33.19.000'
        ]);

        $response->assertJsonValidationErrors('longitude');
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testNavaidNotCreatedWithInvalidLatitudeString()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 'N091.26.33.160',
            'longitude' => 'W003.33.19.000'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Cannot have more than 90 degrees of latitude']);
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testNavaidNotCreatedWithInvalidLongitudeString()
    {
        $response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint, [
            'identifier' => $this->identifier,
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W190.33.19.000'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Cannot have more than 180 degrees of longitude']);
        $this->assertDatabaseMissing('navaids', ['identifier' => $this->identifier]);
    }

    public function testNavaidCanBeCreatedWithValidData()
    {
        $data = [            
            'identifier' => $this->identifier,
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W003.33.19.000'
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
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W003.33.19.000'
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
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W003.33.19.000'
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
