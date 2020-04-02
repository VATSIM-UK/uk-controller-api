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
            ]
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
