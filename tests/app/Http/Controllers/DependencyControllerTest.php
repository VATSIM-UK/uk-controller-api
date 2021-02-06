<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Dependency\Dependency;
use App\Services\DependencyService;
use Carbon\Carbon;

class DependencyControllerTest extends BaseApiTestCase
{
    const APP_URL_KEY = 'app.url';

    public function testItConstructs()
    {
        $this->assertInstanceOf(
            DependencyController::class,
            $this->app->make(DependencyController::class)
        );
    }

    public function testItReturnsDependencies()
    {
        Carbon::setTestNow(Carbon::now());

        $expected = [
            $this->getDependencyData(
                'DEPENDENCY_ONE',
                false,
                Carbon::parse('2020-04-02 21:00:00')
            ),
            $this->getDependencyData(
                'DEPENDENCY_TWO',
                false,
                Carbon::parse('2020-04-03 21:00:00')
            ),
            $this->getDependencyData(
                'USER_DEPENDENCY_ONE',
                true,
                Carbon::parse('2020-04-04 21:00:00')
            ),
            $this->getDependencyData(
                'USER_DEPENDENCY_TWO',
                true,
                Carbon::parse('2020-04-05 21:00:00')
            ),
            $this->getDependencyData('USER_DEPENDENCY_THREE', true, Carbon::now()),
            $this->getDependencyData('DEPENDENCY_THREE', false, Carbon::now()),
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }

    private function getDependencyData(string $key, bool $isUser, Carbon $updatedAtTime): array
    {
        $dependencyNumber = strtolower(substr($key, strrpos($key, '_') + 1));

        return [
            'key' => $key,
            'uri' => sprintf(
                '%s/dependency/%d',
                config(self::APP_URL_KEY),
                Dependency::where('key', $key)->first()->id
            ),
            'local_file' => sprintf(
                '%s.json',
                $isUser ? 'user' . $dependencyNumber : $dependencyNumber
            ),
            'updated_at' => $updatedAtTime->timestamp,
        ];
    }

    private function testItReturnsDependencyData()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'dependency/1')
            ->assertOk()
            ->assertJson(DependencyService::fetchDependencyDataById(1));
    }
}
