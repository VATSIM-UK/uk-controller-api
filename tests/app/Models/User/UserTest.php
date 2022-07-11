<?php

namespace App\Models\User;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use App\Providers\AuthServiceProvider;
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

    /**
     * @dataProvider webTeamProvider
     */
    public function testItCanAccessFilamentIfInTheWebTeam(int $id, bool $expected)
    {
        $this->assertEquals(
            $expected,
            (new User(['id' => $id]))->canAccessFilament()
        );
    }

    public function webTeamProvider(): array
    {
        return [
            [1203533, true],
            [1258635, true],
            [1169992, true],
            [1294298, true],
            [1203534, false],
        ];
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
            12345,
            (new User(['id' => 12345, 'last_name' => 'User']))->name
        );
    }
}
