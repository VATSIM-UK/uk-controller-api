<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

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
            ],
            [
                'key' => 'DEPENDENCY_TWO',
                'uri' => sprintf('%s/dependency/two', config('app.url')),
                'local_file' => 'two.json',
            ]
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
