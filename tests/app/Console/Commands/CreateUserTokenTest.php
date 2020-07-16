<?php
namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Mockery;
use RuntimeException;

class CreateUserTokenTest extends BaseFunctionalTestCase
{
    const ARTISAN_COMMAND = 'token:create';
    
    public function testItConstructs()
    {
        $this->assertInstanceOf(CreateUserTokenTest::class, $this->app->make(CreateUserTokenTest::class));
    }

    public function testItFailsIfVatsimCidInvalid()
    {
        $return = Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => 'notacid']
        );

        $this->assertEquals(1, $return);
        $this->assertEquals('Invalid VATSIM CID' . PHP_EOL, Artisan::output());
    }

    public function testItHandlesNonExistantUser()
    {
        $return = Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => 666]
        );
        $this->assertEquals(2, $return);
        $this->assertEquals('User 666 not found' . PHP_EOL, Artisan::output());
    }

    public function testItCreatesANewToken()
    {
        $tokensBefore = User::findOrFail(1203533)->tokens->count();
        $return = Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => '1203533']
        );

        $tokensAfter = User::findOrFail(1203533)->tokens->count();
        $this->assertGreaterThan($tokensBefore, $tokensAfter);
        $this->assertEquals(0, $return);
        $this->assertNotNull(Artisan::output());
    }
}
