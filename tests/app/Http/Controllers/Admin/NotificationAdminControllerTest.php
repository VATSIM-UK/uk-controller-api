<?php

namespace App\Http\Controllers\Admin;

use App\BaseApiTestCase;
use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use App\Providers\AuthServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationAdminControllerTest extends BaseApiTestCase
{
    use DatabaseTransactions;

	protected static $tokenScope = [
		AuthServiceProvider::SCOPE_DATA_ADMIN
	];

	protected string $baseEndpoint = "admin/notifications";

	public function testActiveNotificationsCanBeRetrievedByDefault()
	{
		// create active notifications
		Notification::factory()->create();
		// create expired notification which should not be included
		Notification::factory()->expired()->create();

        $response = $this->makeAuthenticatedApiRequest('GET', $this->baseEndpoint);

        $response->assertStatus(200);
        $response->assertJsonStructure(['notifications']);
		// test expired notification not included by default.
        $response->assertJsonCount(1, 'notifications');
	}
	
	public function testActiveAndExpiredNotificationsCanBeRetrieved()
	{
		// create active notifications
		Notification::factory()->create();
		// create expired notification which should be included 
        Notification::factory()->expired()->create();

        $response = $this->makeAuthenticatedApiRequest('GET', "{$this->baseEndpoint}?include_expired=true");

        $response->assertStatus(200);
        $response->assertJsonStructure(['notifications']);
		// test expired notification not included by default.
        $response->assertJsonCount(2, 'notifications');
	}

	public function testIndividualNotificationCanBeRetrieved()
	{
        $notification = Notification::factory()->create();

        $response = $this->makeAuthenticatedApiRequest('GET', "{$this->baseEndpoint}/{$notification->id}");

        $response->assertSuccessful();
		$response->assertJson([
			'notification' => [
				...$notification->attributesToArray(),
				// assert controllers are added in full at the positions key.
				'positions' => $notification->controllers->toArray()
			]
		]);
	}

	public function testBadRequestWhenInvalidPositionGiven()
	{
        $invalidPositions = [9999999];
		$response = $this->makeAuthenticatedApiRequest('POST', $this->baseEndpoint,
			$this->generateNotificationBody(['positions' => $invalidPositions])
		);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid positions.']);
	}

	/** @test */
	public function testAllowsCreationOfNotification()
	{
		// create a valid position to create the notification with.
        $position = ControllerPosition::factory()->create();
		
		$body = [
            ...$this->generateNotificationBody(['positions' => [$position->id]]),
			'all_positions' => false,
		];

		$response = $this->makeAuthenticatedApiRequest("POST", $this->baseEndpoint, $body);

        $response->assertStatus(201);
        $response->assertJsonStructure(['notification_id']);
	}

	public function testAllowsCreationOfNotificationAgainstAllPositions()
	{
		$body = $this->generateNotificationBody(['all_positions' => true]);

		$response = $this->makeAuthenticatedApiRequest("POST", $this->baseEndpoint, $body);

        $response->assertStatus(201);
        $response->assertJsonStructure(['notification_id']);

        $createdNotification = Notification::findOrFail((int) $response->json('notification_id'));

		// assert number of allocated positions is equal to all stored positions
        $this->assertEquals(
			$createdNotification->controllers->count(),
			ControllerPosition::count()
		);
	}

	/** @test */
	public function testIgnoresPositionFieldWhenAllPositionsSpecified()
	{
		// create a valid position to create the notification with.
		$position = ControllerPosition::factory()->create();
		$body = [
            ...$this->generateNotificationBody(['positions' => [$position->id]]),
			'all_positions' => true,
		];	

		$response = $this->makeAuthenticatedApiRequest("POST", $this->baseEndpoint, $body);

        $response->assertStatus(201);

		$response->assertJsonStructure(['notification_id']);

        $createdNotification = Notification::findOrFail((int) $response->json('notification_id'));

		// assert number of allocated positions is equal to all stored positions
        $this->assertEquals(
			$createdNotification->controllers->count(),
			ControllerPosition::count()
		);
	}

	/** @dataProvider notificationValidationDataProvider */
	public function testValidatorForNotifications($value, $key, $errorsExpected)
	{
        $response = $this->makeAuthenticatedApiRequest(
			"POST", $this->baseEndpoint, 
			// merge with fake valid notification body to isolate errors
			// from data provider.
			$this->generateNotificationBody([$key => $value])
		);

		if ($errorsExpected) {
            $response->assertStatus(422);
            $response->assertJsonValidationErrorFor($key);
		} else {
            $response->assertSuccessful();
            $response->assertJsonMissingValidationErrors($key);
		}
	}

	public function testValidatesToDateAfterFromDate()
	{
		$response = $this->makeAuthenticatedApiRequest(
			"POST", $this->baseEndpoint,
			$this->generateNotificationBody([
				'valid_from' => Carbon::now()->toIso8601String(),
				'valid_to' => Carbon::now()->subHour()->toIso8601String()
			])
		);

        $response->assertJsonValidationErrorFor('valid_to');
	}
	
	public function testValidatesFromDateBeforeToDate()
	{
		$response = $this->makeAuthenticatedApiRequest(
			"POST", $this->baseEndpoint,
			$this->generateNotificationBody([
				'valid_from' => Carbon::now()->addHour()->toIso8601String(),
				'valid_to' => Carbon::now()->toIso8601String()
			])
		);

        $response->assertJsonValidationErrorFor('valid_from');
	}
	
	public function testNotificationCanBeDeleted()
	{
        $notification = Notification::factory()->create();

		$response = $this->makeAuthenticatedApiRequest(
			"DELETE", "{$this->baseEndpoint}/{$notification->id}"
		);

        $response->assertStatus(204);
        $this->assertSoftDeleted('notifications', ['id' => $notification->id]);
	}

	private function notificationValidationDataProvider() : array // NOSONAR
	{
		return [
			['not-a-url', 'link', true],
			['', 'title', true],
			['This is a valid title', 'title', false],
			['', 'body', true],
			['This is a valid body', 'body', false],
			['', 'valid_from', true],
			['not-a-date', 'valid_from', true],
			['', 'valid_to', true],
			['not-a-date', 'valid_to', true],
		];
	}

	private function generateNotificationBody($overrides = []) : array 
	{
		return array_merge(
			Notification::factory()->make(
				[
					'valid_from' => Carbon::now(), 
					'valid_to' => Carbon::now()->addHours(2)
				]
			)->toArray(),
			$overrides
		);
	}
}
