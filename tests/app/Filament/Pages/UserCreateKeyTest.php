<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use App\Helpers\User\UserConfig;
use App\Services\UserConfigCreatorInterface;
use Livewire\Livewire;
use Mockery;

class UserCreateKeyTest extends BaseFilamentTestCase
{
    private readonly UserConfigCreatorInterface $mockConfigCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConfigCreator = Mockery::mock(UserConfigCreatorInterface::class);
        $this->app->instance(UserConfigCreatorInterface::class, $this->mockConfigCreator);
    }

    public function testItReturnsUnprocessibleIfNoRedirect()
    {
        $this->mockConfigCreator->shouldNotReceive('create');

        Livewire::test(UserCreateKey::class)
            ->assertUnprocessable();
    }

    public function testItReturnsUnprocessibleIfRedirectNotString()
    {
        $this->mockConfigCreator->shouldNotReceive('create');

        Livewire::withQueryParams(['redirect' => 123])
            ->test(UserCreateKey::class)
            ->assertUnprocessable();
    }

    public function testItReturnsUnprocessibleIfRedirectEmpty()
    {
        $this->mockConfigCreator->shouldNotReceive('create');

        Livewire::withQueryParams(['redirect' => ''])
            ->test(UserCreateKey::class)
            ->assertUnprocessable();
    }

    public function testItReturnsUnprocessibleIfRedirectNotUrl()
    {
        $this->mockConfigCreator->shouldNotReceive('create');

        Livewire::withQueryParams(['redirect' => 'ab####'])
            ->test(UserCreateKey::class)
            ->assertUnprocessable();
    }

    public function testItRedirects()
    {
        $this->mockConfigCreator->shouldReceive('create')
            ->with($this->filamentUser()->id)
            ->andReturn(new UserConfig('abcd'));

        Livewire::withQueryParams(['redirect' => urlencode('https://vatsim.uk')])
            ->test(UserCreateKey::class)
            ->assertRedirect('https://vatsim.uk?key=abcd');
    }
}
