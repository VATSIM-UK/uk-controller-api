<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use Carbon\Carbon;

class DependencyControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(DependencyController::class, $this->app->make(DependencyController::class));
    }

    public function testItReturnsDependencies()
    {
        Carbon::setTestNow(Carbon::now());

        $expected = [
            [
                'key' => 'DEPENDENCY_ONE',
                'uri' => sprintf('%s/dependency/one', config('app.url')),
                'local_file' => 'one.json',
                'updated_at' => Carbon::parse('2020-04-02 21:00:00')->timestamp,
            ],
            [
                'key' => 'DEPENDENCY_TWO',
                'uri' => sprintf('%s/dependency/two', config('app.url')),
                'local_file' => 'two.json',
                'updated_at' => Carbon::parse('2020-04-03 21:00:00')->timestamp,
            ],
            [
                'key' => 'USER_DEPENDENCY_ONE',
                'uri' => sprintf('%s/dependency/userone', config('app.url')),
                'local_file' => 'userone.json',
                'updated_at' => Carbon::parse('2020-04-04 21:00:00')->timestamp,
            ],
            [
                'key' => 'USER_DEPENDENCY_TWO',
                'uri' => sprintf('%s/dependency/usertwo', config('app.url')),
                'local_file' => 'usertwo.json',
                'updated_at' => Carbon::parse('2020-04-05 21:00:00')->timestamp,
            ],
            [
                'key' => 'USER_DEPENDENCY_THREE',
                'uri' => sprintf('%s/dependency/userthree', config('app.url')),
                'local_file' => 'userthree.json',
                'updated_at' => Carbon::now()->timestamp,
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
