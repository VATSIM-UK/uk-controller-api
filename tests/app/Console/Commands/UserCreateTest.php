<?php
namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Mockery;
use Symfony\Component\Console\Exception\RuntimeException;

class UserCreateTest extends BaseFunctionalTestCase
{
    const ARTISAN_COMMAND = 'user:create';
    
    public function testItConstructs()
    {
        $this->assertInstanceOf(UserCreate::class, $this->app->make(UserCreate::class));
    }

    public function testItFailsIfVatsimCidNotGiven()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "vatsim_cid").');

        Artisan::call(self::ARTISAN_COMMAND);
    }

    public function testItFailsIfVatsimCidString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(UserCreate::INVALID_CID_MESSAGE);

        Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => 'notacid']
        );
    }

    public function testItFailsIfVatsimCidFloat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(UserCreate::INVALID_CID_MESSAGE);

        Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => 120353.3]
        );
    }

    public function testItFailsIfVatsimCidTooLow()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(UserCreate::INVALID_CID_MESSAGE);

        Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => 400000]
        );
    }

    public function testItFailsIfVatsimCidTooLowBoundary()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(UserCreate::INVALID_CID_MESSAGE);

        Artisan::call(
            self::ARTISAN_COMMAND,
            ['vatsim_cid' => 799999]
        );
    }

    public function testItPassesIfVatsimFounder()
    {
        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('local')
            ->andReturnSelf();

        Storage::shouldReceive('put')
            ->once()
            ->with('access/api-settings-800000.txt', Mockery::any())
            ->andReturnSelf();

        $this->assertEquals(0, Artisan::call(self::ARTISAN_COMMAND, ['vatsim_cid' => 800000]));
    }

    public function testItPassesNormalCid()
    {
        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('local')
            ->andReturnSelf();

        Storage::shouldReceive('put')
            ->once()
            ->with('access/api-settings-1203555.txt', Mockery::any())
            ->andReturnSelf();

        $this->assertEquals(0, Artisan::call(self::ARTISAN_COMMAND, ['vatsim_cid' => 1203555]));
    }

    public function testItPassesNewMember()
    {
        Storage::shouldReceive('disk')
            ->zeroOrMoreTimes()
            ->with('local')
            ->andReturnSelf();

        Storage::shouldReceive('put')
            ->once()
            ->with('access/api-settings-1402313.txt', Mockery::any())
            ->andReturnSelf();

        $this->assertEquals(0, Artisan::call(self::ARTISAN_COMMAND, ['vatsim_cid' => 1402313]));
    }
}
