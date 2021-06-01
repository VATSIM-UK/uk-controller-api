<?php 

namespace App\Http\Controllers\Admin;

use App\BaseApiTestCase;
use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HoldAdminControllerTest extends BaseApiTestCase
{
    use DatabaseTransactions;
    
    protected Navaid $navaid;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_DATA_ADMIN,
    ];
 
    public function setUp() : void
    {
        parent::setUp();

        $this->navaid = Navaid::factory()->create();
    }

    public function testNavaidHoldsCanBeRetrieved()
    {
        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);

        $response = $this->makeAuthenticatedApiRequest('GET', "admin/navaids/{$this->navaid->identifier}/holds");
        $hold['restrictions'] = [];
        
        $response->assertStatus(200);
        $response->assertJson(['holds' => [$hold->toArray()]]);
    }

    public function testNavaidHoldCanBeCreated()
    {
        $data = $this->generateHoldData();

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertStatus(201);

        $expected = array_merge(['navaid_id' => $this->navaid->id], $data);

        $this->assertDatabaseHas('holds', $expected);
    }

    public function testInboundHeadingCannotBeGreaterThan360Degrees()
    {
        $data = $this->generateHoldData(['inbound_heading' => 361]);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonValidationErrors('inbound_heading');
    }

    public function testInboundHeadingCannotBeZeroDegrees()
    {
        $data = $this->generateHoldData(['inbound_heading' => 0]);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonValidationErrors('inbound_heading');
    }

    public function testTurnDirectionCanOnlyBeLeftOrRightString()
    {
        $data = $this->generateHoldData(['turn_direction' => 'foo']);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonValidationErrors('turn_direction');
    }

    public function testMinimumAltitudeMustBeCannotBeLessThanFourDigits()
    {
        $data = $this->generateHoldData(['minimum_altitude' => 500]);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonValidationErrors('minimum_altitude');
    }

    public function testMaximumAltitudeCannotBeLessThanFourDigits()
    {        
        $data = $this->generateHoldData(['maximum_altitude' => 500]);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonValidationErrors('maximum_altitude');
    }

    public function testDescriptionFallsBackToNavaidWhenNotSpecified()
    {
        $data = $this->generateHoldData(['description' => null]);

        $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $this->assertDatabaseHas('holds', ['navaid_id' => $this->navaid->id, 'description' => $this->navaid->identifier]);
    }

    public function testTurnDirectionCanBeLeftString()
    {
        $data = $this->generateHoldData(['turn_direction' => 'left']);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonMissingValidationErrors('turn_direction');
    }


    public function testTurnDirectionCanBeRightString()
    {
        $data = $this->generateHoldData(['turn_direction' => 'right']);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonMissingValidationErrors('turn_direction');
    }

    public function testTurnDirectionCannotBeInvalidString()
    {
        $data = $this->generateHoldData(['turn_direction' => 'foo']);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);

        $response->assertJsonValidationErrors('turn_direction');
    }

    public function testDescriptionUniqueWhenSpecified()
    {
        $description = 'Exsiting Hold';

        // create existing hold with the same description
        Hold::factory()->create(['description' => $description, 'navaid_id' => $this->navaid->id]);

        $data = $this->generateHoldData(['description' => $description]);

        $response = $this->makeAuthenticatedApiRequest('POST', "admin/navaids/{$this->navaid->identifier}/holds", $data);
        $response->assertStatus(409);
        $response->assertJson(['message' => 'Description of hold already used.']);
    }

    public function testHoldCanBeModified()
    {
        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);

        $minimum_altitude = 4000;

        $response = $this->makeAuthenticatedApiRequest('PUT', "admin/navaids/{$this->navaid->identifier}/holds/{$hold->id}",
            $this->generateHoldData(['minimum_altitude' => $minimum_altitude, 'description' => $hold->description])
        );

        $response->assertStatus(204);
        
        $this->assertDatabaseHas('holds', [
            'id' => $hold->id,
            'minimum_altitude' => $minimum_altitude
        ]);
    }

    public function testHoldModificationChecksForDescriptionUniquenessWhenChanged()
    {
        $conflictingDescription = 'New Description';
        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);
        Hold::factory()->create(['navaid_id' => $this->navaid->id, 'description' => $conflictingDescription]);

        $response = $this->makeAuthenticatedApiRequest('PUT', "admin/navaids/{$this->navaid->identifier}/holds/{$hold->id}",
            $this->generateHoldData(['description' => $conflictingDescription])
        );

        $response->assertStatus(409);
    }

    public function testHoldModificationChecksHoldAssociatedWithNavaid()
    {
        $otherNavaid = Navaid::factory()->create();
        $hold = Hold::factory()->create(['navaid_id' => $otherNavaid->id]);

        $response = $this->makeAuthenticatedApiRequest('PUT', "admin/navaids/{$this->navaid->identifier}/holds/{$hold->id}",
            $this->generateHoldData()
        );

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Hold not associated with Navaid.']);
    }

    public function testHoldCanBeDeleted()
    {
        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);

        $response = $this->makeAuthenticatedApiRequest('DELETE', "admin/navaids/{$this->navaid->identifier}/holds/{$hold->id}");

        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('holds', [
            'id' => $hold->id
        ]);
    }

    public function testHoldCannotBeDeletedWhenNotAssociatedWithNavaid()
    {
        $otherNavaid = Navaid::factory()->create();
        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);

        $response = $this->makeAuthenticatedApiRequest('DELETE', "admin/navaids/{$otherNavaid->identifier}/holds/{$hold->id}");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Hold not associated with Navaid.']);
    }

    public function testIndividualHoldCanBeRetrieved()
    {
        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);

        $response = $this->makeAuthenticatedApiRequest('GET', "admin/navaids/{$this->navaid->identifier}/holds/{$hold->id}");

        $response->assertJson(['hold' => $hold->load('restrictions')->toArray()]);
    }

    public function testHoldCannotBeRetrievedWithBadNavaid()
    {
        $otherNavaid = Navaid::factory()->create();

        $hold = Hold::factory()->create(['navaid_id' => $this->navaid->id]);

        $response = $this->makeAuthenticatedApiRequest('GET', "admin/navaids/{$otherNavaid->identifier}/holds/{$hold->id}");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Hold not associated with Navaid.']);
    }
 
    private function generateHoldData(array $overrides = [])
    {
        return array_merge(
            [
                'inbound_heading' => 180,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'left',
                'description' => 'FOO'    
            ],
            $overrides
        );
    }
}