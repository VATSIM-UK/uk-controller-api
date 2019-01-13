<?php
namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Symfony\Component\Console\Exception\RuntimeException;

class UserCreateTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(UserCreate::class, $this->app->make(UserCreate::class));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "vatsim_cid").
     */
    public function testItFailsIfVatsimCidNotGiven()
    {
        Artisan::call('user:create');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid VATSIM CID provided.
     */
    public function testItFailsIfVatsimCidString()
    {
        Artisan::call(
            'user:create',
            ['vatsim_cid' => 'notacid']
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid VATSIM CID provided.
     */
    public function testItFailsIfVatsimCidFloat()
    {
        Artisan::call(
            'user:create',
            ['vatsim_cid' => 120353.3]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid VATSIM CID provided.
     */
    public function testItFailsIfVatsimCidTooLow()
    {
        Artisan::call(
            'user:create',
            ['vatsim_cid' => 400000]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid VATSIM CID provided.
     */
    public function testItFailsIfVatsimCidTooLowBoundary()
    {
        Artisan::call(
            'user:create',
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

        $this->assertEquals(0, Artisan::call('user:create', ['vatsim_cid' => 800000]));
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

        $this->assertEquals(0, Artisan::call('user:create', ['vatsim_cid' => 1203555]));
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

        $this->assertEquals(0, Artisan::call('user:create', ['vatsim_cid' => 1402313]));
    }
}
