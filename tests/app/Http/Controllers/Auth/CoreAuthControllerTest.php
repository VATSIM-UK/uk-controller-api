<?php

namespace App\Http\Controllers\Auth;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User;
use Mockery;

class CoreAuthControllerTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfMinute());
    }

    public function testItRedirectsToBasePage()
    {
        $this->get('/')
            ->assertRedirect('admin/login');
    }

    public function testItRedirectsToSocialiteLogin()
    {
        Socialite::shouldReceive('driver->redirect')
            ->andReturn(Redirect::away('https://vatsim.uk/oauth/authorize'));

        $this->get('auth/redirect')
            ->assertRedirect('https://vatsim.uk/oauth/authorize');
    }

    public function testInvalidStateFromSocialiteCausesAbort()
    {
        Socialite::shouldReceive('driver->user')->andThrow(new InvalidStateException);
        $this->get('auth/callback')
            ->assertUnauthorized();
    }

    public function testItCreatesAUserOnCallback()
    {
        $socialiteUser = Mockery::mock(User::class);
        $socialiteUser->shouldReceive('getId')
            ->andReturn(1234)
            ->andSet('first_name', 'Test')
            ->andSet('last_name', 'User');
        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $this->get('auth/callback')
            ->assertRedirect('admin');

        $this->assertDatabaseHas(
            'user',
            [
                'id' => 1234,
                'first_name' => 'Test',
                'last_name' => 'User',
            ]
        );
    }

    public function testItUpdatesAUserOnCallback()
    {
        $socialiteUser = Mockery::mock(User::class);
        $socialiteUser->shouldReceive('getId')->andReturn(self::ACTIVE_USER_CID)
            ->andSet('first_name', 'Test')
            ->andSet('last_name', 'User');
        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $this->get('auth/callback')
            ->assertRedirect('admin');

        $this->assertDatabaseHas(
            'user',
            [
                'id' => self::ACTIVE_USER_CID,
                'first_name' => 'Test',
                'last_name' => 'User',
            ]
        );
    }
}
