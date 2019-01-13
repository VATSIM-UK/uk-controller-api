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
            ->seeStatusCode(403);
    }

    public function testCreateAssignmentRejectsTokensWithoutUserScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'squawk-assignment/BAW123')
            ->seeStatusCode(403);
    }

    public function testItRejectGetAssignmentRequestsWithMissingCallsigns()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/'
        )->assertResponseStatus(404);
    }

    public function testItRejectsGetAssignmentRequestsWithCallsignTooLong()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/01234567890'
        )->assertResponseStatus(404);
    }

    public function testAssignGeneralSquawkReturnsSquawk()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/BAW123'
        )
            ->seeJsonEquals(
                [
                    'squawk' => '4723',
                ]
            )->assertResponseStatus(200);
    }

    public function testAssignGeneralSquawkAssignemtnReturnsNotFoundIfAssignementNotFound()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_GET,
            'squawk-assignment/BAW12AZ'
        )
            ->seeJsonEquals(
                [
                    'message' => 'Squawk assignment not found for BAW12AZ',
                ]
            )->assertResponseStatus(404);
    }

    public function testCheckAssignGeneralSquawkFailsIfNoType()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['origin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    
    public function testCheckAssignGeneralSquawkFailsIfTypeInvalid()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'special', 'origin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckGetGeneralSquawkFailsIfOriginMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'notOrigin' => 'EGLL', 'destination' => 'LFPG']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfDestinationMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => 'EGLL', 'notdestination' => 'LFPG']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfDestinationGivenIncorrectly()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => 'EGLL', 'destination' => '1234']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignGeneralSquawkFailsIfOriginGivenIncorrectly()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => '1234', 'destination' => 'EGKK']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testAssignGeneralSquawkReturnsSquawkOnNewAssignment()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW436',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->seeJson(
                [
                    'squawk' => '1234',
                ]
            )->assertResponseStatus(201);
    }

    public function testAssignGeneralSquawkReturnsSquawkOnUpdateExistingAssignment()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->seeJson(
                [
                    'squawk' => '1234',
                ]
            )->assertResponseStatus(200);
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
            ->seeJson(
                [
                    'squawk' => '1234',
                ]
            )->assertResponseStatus(201);

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW437',
            ['type' => 'general', 'origin' => 'EGKK', 'destination' => 'EGCC']
        )
            ->seeJson(
                [
                    'message' => 'Unable to allocate squawk from available ranges for BAW437',
                    'squawk' => SquawkController::FAILURE_SQUAWK,
                ]
            )->assertResponseStatus(500);
    }

    public function testCheckAssignLocalSquawkFailsIfUnitMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'notUnit' => 'EGLL', 'rules' => 'I']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignLocalSquawkFailsIfRulesMissing()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGLL', 'notrules' => 'I']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignLocalSquawkFailsIfRulesInvalid()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGLL', 'rules' => 'X']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignLocalSquawkDoesNotAcceptAnyRules()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGLL', 'rules' => 'A']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Request is missing required data',
                ]
            )->assertResponseStatus(400);
    }

    public function testCheckAssignLocalSquawkReturnsSquawkWhenNewAssignmentCreated()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW9AX',
            ['type' => 'local', 'unit' => 'EGKA', 'rules' => 'I']
        )
            ->seeJsonEquals(
                [
                    'squawk' => '3762',
                ]
            )->assertResponseStatus(201);
    }

    public function testCheckAssignLocalSquawkReturnsSquawkWhenExistingAssignmentUpdated()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW123',
            ['type' => 'local', 'unit' => 'EGKA', 'rules' => 'I']
        )
            ->seeJsonEquals(
                [
                    'squawk' => '3762',
                ]
            )->assertResponseStatus(200);
    }

    public function testCheckAssignLocalSquawkReturnsErrorWhenSquawkNotFound()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'squawk-assignment/BAW9AX',
            ['type' => 'local', 'unit' => 'EGNA', 'rules' => 'I']
        )
            ->seeJsonEquals(
                [
                    'message' => 'Unable to allocate local squawk for BAW9AX',
                    'squawk' => SquawkController::FAILURE_SQUAWK,
                ]
            )->assertResponseStatus(500);
    }

    public function testItRejectsAssignmentDeletionRequestsWithMissingCallsigns()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/'
        )->assertResponseStatus(404);
    }

    public function testItRejectsAssignmentDeletionRequestsWithCallsignTooLong()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/01234567890'
        )->assertResponseStatus(404);
    }

    public function testResponseWhenAssignmentIsSuccessfullyDeleted()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/BAW123'
        )->assertResponseStatus(204);
    }

    public function testResponseWhenAssignmentIsNotDeleted()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_DELETE,
            'squawk-assignment/NOTREALCS'
        )->assertResponseStatus(204);
    }
}
