<?php

namespace App\Http\Controllers\Admin;

use App\BaseApiTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Aircraft\WakeCategory;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StandAdminControllerTest extends BaseApiTestCase
{
    use DatabaseTransactions;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_DATA_ADMIN,
    ];

    private Airfield $airfield;

    public function setUp(): void
    {
        parent::setUp();

        $this->airfield = Airfield::factory()->create();
    }

    public function testStandTypesCanBeRetrieved()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/stand-types");

        $response->assertStatus(200);
    }

    public function testStandsCanBeRetrievedForAirfield()
    {
        $stand = Stand::factory()->create(['airfield_id' => $this->airfield->id]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields/{$this->airfield->code}/stands");

        $response->assertStatus(200);
        $response->assertJsonStructure(['stands']);
    }

    public function testAirfieldStandCanBeRetrieved()
    {
        $stand = Stand::factory()->create(['airfield_id' => $this->airfield->id]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['stand']);
    }

    public function testAirfieldsStandsNotFoundWhenStandIsNotPartOfAirfield()
    {
        $stand = Stand::factory()->create();

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Stand not part of airfield.']);
    }

    public function testAirfieldsWithStandsCanBeRetrieved()
    {

        $airfieldWithStands = Airfield::factory()->create();
        Stand::factory()->create(['airfield_id' => $airfieldWithStands->id]);


        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields");

        $response->assertStatus(200);
        $response->assertJsonFragment($airfieldWithStands->toArray(), true);
        $response->assertJsonMissing($this->airfield->toArray(), true);
    }

    public function testAirfieldsWithoutStandsCanBeRetrieved()
    {

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, "admin/airfields?all=true");

        $response->assertStatus(200);
        $response->assertJsonFragment($this->airfield->toArray(), true);
    }

    public function testValidatesInvalidStandType()
    {
        $invalidStandType = 999;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['type_id' => $invalidStandType]);

        $response->assertJsonValidationErrors(['type_id']);
    }

    public function testAllowsStandTypeWhichExists()
    {
        $standType = StandType::first()->id;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['type_id' => $standType]);

        $response->assertJsonMissingValidationErrors(['type_id']);
    }

    public function testValidatesLatitudeLowerBound()
    {
        $invalidLatitude = -90.001;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['latitude' => $invalidLatitude]);

        $response->assertJsonValidationErrors(['latitude']);
    }

    public function testValidatesLatitudeUpperBound()
    {
        $invalidLatitude = 90.001;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['latitude' => $invalidLatitude]);

        $response->assertJsonValidationErrors(['latitude']);
    }

    public function testAllowsValidLatitude()
    {
        $validLatitude = 54.332;

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['latitude' => $validLatitude]);

        $response->assertJsonMissingValidationErrors(['latitude']);
    }
    public function testValidatesLongitudeLowerBound()
    {
        $invalidLongitude = -180.01;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['longitude' => $invalidLongitude]);

        $response->assertJsonValidationErrors(['longitude']);
    }

    public function testValidatesLongitudeUpperBound()
    {
        $invalidLongitude = 180.001;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['longitude' => $invalidLongitude]);

        $response->assertJsonValidationErrors(['longitude']);
    }

    public function testAllowsValidLongitude()
    {
        $validLatitude = -10.2494;

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['longitude' => $validLatitude]);

        $response->assertJsonMissingValidationErrors(['longitude']);
    }

    public function testAllowsNullTerminal()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['terminal_id' => null]);

        $response->assertJsonMissingValidationErrors(['terminal_id']);
    }

    public function testAllowsAbsentTerminal()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", []);

        $response->assertJsonMissingValidationErrors(['terminal_id']);
    }

    public function testValidatesTerminalExistenceWhenSpecified()
    {
        $invalidTerminalId = 999;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['terminal_id' => $invalidTerminalId]);

        $response->assertJsonValidationErrors(['terminal_id']);
    }

    public function testBadRequestWhenTerminalIsntInAirfield()
    {
        $otherAirfield = Airfield::factory()->create();
        $terminal = Terminal::factory()->create(['airfield_id' => $otherAirfield]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", $this->generateStandData(['terminal_id' => $terminal->id]));

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid terminal for airfield.']);
    }

    public function testValidationSucceedsWhenTerminalSpecified()
    {
        $terminal = Terminal::factory()->create();

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['terminal_id' => $terminal->id]);

        $response->assertJsonMissingValidationErrors(['terminal_id']);
    }

    public function testValidatesWakeCategoryExists()
    {
        $invalidWakeCategory = 999;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['wake_category_id' => $invalidWakeCategory]);

        $response->assertJsonValidationErrors(['wake_category_id']);
    }

    public function testValidationSucceedsWhenWakeCategoryExists()
    {
        $wakeCategory = WakeCategory::first();
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['wake_category_id' => $wakeCategory->id]);

        $response->assertJsonMissingValidationErrors(['wake_category_id']);        
    }

    public function testValidatesMaxAircraftIdExistsWhenSpecified()
    {
        $invalidAircraftId = 999;
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['max_aircraft_id' => $invalidAircraftId]);

        $response->assertJsonValidationErrors(['max_aircraft_id']);
    }

    public function testAllowsNullMaxAircraftId()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['max_aircraft_id' => null]);

        $response->assertJsonMissingValidationErrors(['max_aircraft_id']);
    }

    public function testIgnoresValidationOnMaxAircraftWhenNotSpecified()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", $this->generateStandData());

        $response->assertJsonMissingValidationErrors(['max_aircraft_id']);
    }

    public function testValidatesForStandIdentifier()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", []);

        $response->assertJsonValidationErrors(['identifier']);
    }

    public function testValidatesForUniqueStandIdentifier()
    {
        $stand = Stand::factory()->create(['airfield_id' => $this->airfield->id]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", $this->generateStandData(['identifier' => $stand->identifier, 'terminal_id' => null]));

        $response->assertStatus(409);
        $response->assertJson(['message' => 'Stand identifier in use for airfield.']);
    }

    public function testValidatesForPositiveAssignmentPriority()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['assignment_priority' => -1]);

        $response->assertJsonValidationErrors(['assignment_priority']);
    }

    public function testAssignmentPriorityNotMandatory()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", []);

        $response->assertJsonMissingValidationErrors(['assignment_priority']);
    }

    public function testAssignmentPriorityAllowsPositiveNumber()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", ['assignment_priority' => 100]);

        $response->assertJsonMissingValidationErrors(['assignment_priority']);
    }

    public function testCreatesStandWithValidFields()
    {
        $validTerminal = Terminal::factory()->create(['airfield_id' => $this->airfield->id]);

        $data = $this->generateStandData(['terminal_id' => $validTerminal->id]);
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['stand_id']);

        $this->assertDatabaseHas('stands', [
            'airfield_id' => $this->airfield->id,
            'identifier' => $data['identifier'],
            'type_id' => $data['type_id'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'terminal_id' => $data['terminal_id'],
            'wake_category_id' => $data['wake_category_id']
        ]);
    }

    public function testCreatesStandWithValidFieldsWithoutTerminal()
    {
        $data = $this->generateStandData(['terminal_id' => null]);
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_POST, "admin/airfields/{$this->airfield->code}/stands", $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['stand_id']);

        $this->assertDatabaseHas('stands', [
            'airfield_id' => $this->airfield->id,
            'identifier' => $data['identifier'],
            'type_id' => $data['type_id'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'terminal_id' => $data['terminal_id'],
            'wake_category_id' => $data['wake_category_id']
        ]);
    }

    public function testStandCanBeModified()
    {
        $terminal = Terminal::factory()->create(['airfield_id' => $this->airfield->id]);
        $stand = Stand::factory()->create(['airfield_id' => $this->airfield->id, 'terminal_id' => $terminal->id]);
        $changedIdentifier = "215L";

        $data = $this->generateStandData(['identifier' => $changedIdentifier, 'terminal_id' => $terminal->id]);
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}", $data);

        $response->assertStatus(204);
        
        $this->assertDatabaseHas('stands', [
            'id' => $stand->id,
            'identifier' => $changedIdentifier
        ]);
    }

    public function testStandCantBeModifiedWhenNotInAirfield()
    {
        $stand = Stand::factory()->create();

        $data = $this->generateStandData();
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}", $data);

        $response->assertStatus(404); 
        $response->assertJson(['message' => 'Stand not part of airfield.']);
    }

    public function testStandCantBeModifiedToExistingIdentifier()
    {
        $identifier = '541C';

        Stand::factory()->create(['airfield_id' => $this->airfield->id, 'identifier' => $identifier]);
        $stand = Stand::factory()->create(['airfield_id' => $this->airfield->id]);

        $data = $this->generateStandData(['identifier' => $identifier, 'terminal_id' => $stand->terminal_id]);
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_PUT, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}", $data);


        $response->assertStatus(409);
        $response->assertJson(['message' => 'Stand identifier in use for airfield.']);
    }

    public function testDeletesStand()
    {
        $stand = Stand::factory()->create(['airfield_id' => $this->airfield->id]);

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('stands', [
            'airfield_id' => $this->airfield->id,
            'id' => $stand->id
        ]);
    }

    public function testDoesntDeleteStandWhenNotPartOfAirfield()
    {
        $stand = Stand::factory()->create();

        $response = $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, "admin/airfields/{$this->airfield->code}/stands/{$stand->id}");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Stand not part of airfield.']);
    }

    private function generateStandData(array $overrides = []) : array
    {
        return array_merge([
            'identifier' => '213L',
            'type_id' => StandType::first()->id,
            'latitude' => 54.01,
            'longitude' => 4.01,
            'terminal_id' => Terminal::factory()->create()->id,
            'wake_category_id' => WakeCategory::first()->id
        ], $overrides);
    }
}