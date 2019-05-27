<?php
namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Squawks\Range;

class SquawkControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(SquawkController::class, $this->app->make(SquawkController::class));
    }

    public function testAssignGeneralSquawkDoesNotAcceptPost()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'squawk-assignment/BAW123');
        $this->assertEquals(405, $this->response->getStatusCode());
    }

    public function testGetAssignmentRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'squawk-assignment/BAW123')
            ->assertStatus(403);
    }

    public function testCreateAssignmentRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'squawk-assignment/BAW123')
            ->assertStatus(403);
    }

    public function testItRejectGetAssignmentRequestsWithMissingCallsigns()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/'
        )->assertStatus(404);
    }

    public function testItRejectsGetAssignmentRequestsWithCallsignTooLong()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/01234567890'
        )->assertStatus(404);
    }

    public function testAssignGeneralSquawkReturnsSquawk()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/BAW123'
        )
            ->assertJsonEquals(
                [
                    'squawk' => '4723',
                ]
            )->assertStatus(200);
    }

    public function testAssignGeneralSquawkAssignemtnReturnsNotFoundIfAssignementNotFound()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/BAW12AZ'
        )
            ->assertJsonEquals(
                [
                    'message' => 'Squawk assignment not found for BAW12AZ',
                ]
            )->assertStatus(404);
    }

    public function testCheckAssignGeneralSquawkFailsIfNoType()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['origin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    
    public function testCheckAssignGeneralSquawkFailsIfTypeInvalid()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'special', 'origin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckGetGeneralSquawkFailsIfOriginMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'notOrigin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfDestinationMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => 'EGLL', 'notdestination' => 'LFPG']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfDestinationGivenIncorrectly()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => 'EGLL', 'destination' => '1234']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfOriginGivenIncorrectly()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => '1234', 'destination' => 'EGKK']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testAssignGeneralSquawkReturnsSquawkOnNewAssignment()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW436',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->assertJson(
                [
                    'squawk' => '1234',
                ]
            )->assertStatus(201);
    }

    public function testAssignGeneralSquawkReturnsSquawkOnUpdateExistingAssignment()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->assertJson(
                [
                    'squawk' => '1234',
                ]
            )->assertStatus(200);
    }

    public function testAssignGeneralSquawkResponseReturnsErrorIfFailureToAssign()
    {
        // For this test only, we need to drop the possible ranges.
        Range::where('id', '!=', 1)->delete();
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW436',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->assertJson(
                [
                    'squawk' => '1234',
                ]
            )->assertStatus(201);

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW437',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->assertJson(
                [
                    'message' => 'Unable to allocate squawk from available ranges for BAW437',
                    'squawk' => SquawkController::FAILURE_SQUAWK,
                ]
            )->assertStatus(500);
    }

    public function testCheckAssignLocalSquawkFailsIfUnitMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'notUnit' => 'EGLL', 'rules' => 'I']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkFailsIfRulesMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGLL', 'notrules' => 'I']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkFailsIfRulesInvalid()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGLL', 'rules' => 'X']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkDoesNotAcceptAnyRules()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGLL', 'rules' => 'A']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkReturnsSquawkWhenNewAssignmentCreated()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW9AX',
            ['type' => 'local', 'unit' => 'EGKA', 'rules' => 'I']
        )
            ->assertJsonEquals(
                [
                    'squawk' => '3762',
                ]
            )->assertStatus(201);
    }

    public function testCheckAssignLocalSquawkReturnsSquawkWhenExistingAssignmentUpdated()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGKA', 'rules' => 'I']
        )
            ->assertJsonEquals(
                [
                    'squawk' => '3762',
                ]
            )->assertStatus(200);
    }

    public function testCheckAssignLocalSquawkReturnsErrorWhenSquawkNotFound()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW9AX',
            ['type' => 'local', 'unit' => 'EGNA', 'rules' => 'I']
        )
            ->assertJsonEquals(
                [
                    'message' => 'Unable to allocate local squawk for BAW9AX',
                    'squawk' => SquawkController::FAILURE_SQUAWK,
                ]
            )->assertStatus(500);
    }

    public function testItRejectsAssignmentDeletionRequestsWithMissingCallsigns()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/'
        )->assertStatus(404);
    }

    public function testItRejectsAssignmentDeletionRequestsWithCallsignTooLong()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/01234567890'
        )->assertStatus(404);
    }

    public function testResponseWhenAssignmentIsSuccessfullyDeleted()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/BAW123'
        )->assertStatus(204);
    }

    public function testResponseWhenAssignmentIsNotDeleted()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/NOTREALCS'
        )->assertStatus(204);
    }
}
