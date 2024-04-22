<?php

namespace App\Models\User;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use App\Providers\AuthServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use TestingUtils\Traits\WithSeedUsers;

class UserTest extends BaseFunctionalTestCase
{
    use WithSeedUsers;

    public function testItConstructs()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testItCanBeBanned()
    {
        $user = $this->activeUser();
        $user->ban();

        $this->assertTrue($this->activeUser()->accountStatus->banned);
    }

    public function testItCanBeDisabled()
    {
        $user = $this->activeUser();
        $user->disable();

        $this->assertTrue($this->activeUser()->accountStatus->disabled);
    }

    public function testItCanBeActivated()
    {
        $user = $this->activeUser();
        $user->disable();
        $user->activate();

        $this->assertTrue($this->activeUser()->accountStatus->active);
    }

    public function testItCanHaveALastLogin()
    {
        Carbon::setTestNow(Carbon::now());
        $this->activeUser()->touchLastLogin();

        $this->assertEquals(Carbon::now(), $this->activeUser()->last_login);
    }

    public function testItCanBeSerializedToJson()
    {
        $token = User::findOrFail(1203533)->createToken('access', [AuthServiceProvider::SCOPE_USER])->token->id;

        $jsonData = User::findOrFail(1203533)->jsonSerialize();
        $this->assertEquals(1203533, $jsonData['id']);
        $this->assertEquals(UserStatus::STATUS_MESSAGES[UserStatus::ACTIVE], $jsonData['status']);
        $this->assertInstanceOf(Collection::class, $jsonData['tokens']);
        $this->assertEquals($token, $jsonData['tokens']->first()->id);
    }

    public function testItCanAccessDefaultFilamentPanel()
    {
        $this->assertTrue((new User(['id' => 1234]))->canAccessPanel(Filament::getPanel('admin')));
    }

    public function testItCantAccessOtherFilamentPanels()
    {
        $this->assertFalse((new User(['id' => 1234]))->canAccessPanel(Panel::make()->id('foo')));
    }

    public function testItHasAFilamentName()
    {
        $this->assertEquals(
            'Test User',
            (new User(['first_name' => 'Test', 'last_name' => 'User']))->getFilamentName()
        );
    }

    public function testItHasANameAttribute()
    {
        $this->assertEquals(
            'Test User',
            (new User(['id' => 12345, 'first_name' => 'Test', 'last_name' => 'User']))->name
        );
    }
}
