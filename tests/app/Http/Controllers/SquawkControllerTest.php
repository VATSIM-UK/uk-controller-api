<?php
namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Models\Squawks\Range;

class SquawkControllerTest extends BaseApiTestCase
{
    const SQUAWK_ASSIGNMENT_URI = 'squawk-assignment/BAW123';
    const MISSING_DATA_EXPECTED_MESSAGE = 'Request is missing required data';

    public function testItConstructs()
    {
        $this->assertInstanceOf(SquawkController::class, $this->app->make(SquawkController::class));
    }

    public function testAssignGeneralSquawkDoesNotAcceptPost()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, self::SQUAWK_ASSIGNMENT_URI)
            ->assertStatus(405);
    }

    public function testGetAssignmentRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, self::SQUAWK_ASSIGNMENT_URI)
            ->assertStatus(403);
    }

    public function testCreateAssignmentRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, self::SQUAWK_ASSIGNMENT_URI)
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

    public function testGetSquawkAssignmentReturnsSquawk()
    {
        CcamsSquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0707']);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            self::SQUAWK_ASSIGNMENT_URI
        )
            ->assertJson(
                [
                    'squawk' => '0707',
                ]
            )->assertStatus(200);
    }

    public function testAssignGeneralSquawkAssignmentReturnsNotFoundIfAssignmentNotFound()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/BAW12AZ'
        )
            ->assertJson(
                [
                    'message' => 'Assignment not found for BAW12AZ',
                ]
            )->assertStatus(404);
    }

    public function testCheckAssignGeneralSquawkFailsIfNoType()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['origin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }


    public function testCheckAssignGeneralSquawkFailsIfTypeInvalid()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'special', 'origin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckGetGeneralSquawkFailsIfOriginMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'general', 'notOrigin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfDestinationMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'general', 'origin' => 'EGLL', 'notdestination' => 'LFPG']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfDestinationGivenIncorrectly()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'general', 'origin' => 'EGLL', 'destination' => '1234']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfOriginGivenIncorrectly()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'general', 'origin' => '1234', 'destination' => 'EGKK']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
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
                    'squawk' => '0303',
                ]
            )->assertStatus(201);
    }

    public function testAssignGeneralSquawkReturnsSquawkOnUpdateExistingAssignment()
    {
        // For this test only, we need to drop the possible ranges.
        CcamsSquawkRange::query()->delete();
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW437',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->assertJson(
                [
                    'message' => 'Unable to allocate general squawk for BAW437',
                    'squawk' => SquawkController::FAILURE_SQUAWK,
                ]
            )->assertStatus(500);
    }

    public function testAssignGeneralSquawkResponseReturnsErrorIfFailureToAssign()
    {
        // For this test only, we need to drop the possible ranges.
        CcamsSquawkRange::query()->delete();
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW437',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->assertJson(
                [
                    'message' => 'Unable to allocate general squawk for BAW437',
                    'squawk' => SquawkController::FAILURE_SQUAWK,
                ]
            )->assertStatus(500);
    }

    public function testCheckAssignLocalSquawkFailsIfUnitMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'local', 'notUnit' => 'EGLL', 'rules' => 'I']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkFailsIfRulesMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'local', 'unit' => 'EGLL', 'notrules' => 'I']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkFailsIfRulesInvalid()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'local', 'unit' => 'EGLL', 'rules' => 'X']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkDoesNotAcceptAnyRules()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::SQUAWK_ASSIGNMENT_URI,
            ['type' => 'local', 'unit' => 'EGLL', 'rules' => 'A']
        )
            ->assertJson(
                [
                    'message' => self::MISSING_DATA_EXPECTED_MESSAGE,
                ]
            )->assertStatus(400);
    }

    public function testCheckAssignLocalSquawkReturnsSquawkWhenNewAssignmentCreated()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW9AX',
            ['type' => 'local', 'unit' => 'EGKK', 'rules' => 'I']
        )
            ->assertJson(
                [
                    'squawk' => '0202',
                ]
            )->assertStatus(201);
    }

    public function testCheckAssignLocalSquawkReturnsErrorWhenSquawkNotFound()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW9AX',
            ['type' => 'local', 'unit' => 'EGNA', 'rules' => 'I']
        )
            ->assertJson(
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
            self::SQUAWK_ASSIGNMENT_URI
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
