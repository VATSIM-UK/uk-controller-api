<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User\User;
use App\Providers\AuthServiceProvider;

class DeleteUserTokensTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $command = new DeleteUserTokens();
        $this->assertInstanceOf(DeleteUserTokens::class, $command);
    }

    public function testItFailsIfCidBad()
    {
        $return = Artisan::call('tokens:delete-user', ['vatsim_cid' => 'foo']);
        $this->assertEquals(1, $return);
        $this->assertEquals('Invalid VATSIM CID' . PHP_EOL, Artisan::output());
    }

    public function testItFailsIfUserNotFound()
    {
        $return = Artisan::call('tokens:delete-user', ['vatsim_cid' => '666']);
        $this->assertEquals(2, $return);
        $this->assertEquals('User 666 not found' . PHP_EOL, Artisan::output());
    }

    public function testItDeletesAllUserTokens()
    {
        User::findOrFail(1203533)->createToken('access', [AuthServiceProvider::SCOPE_USER]);
        $return = Artisan::call('tokens:delete-user', ['vatsim_cid' => '1203533']);
        $this->assertEquals(0, $return);
        $this->assertEquals('All access tokens deleted for user 1203533' . PHP_EOL, Artisan::output());
        $this->assertEquals(0, User::findOrFail(1203533)->tokens->count());
    }
}
