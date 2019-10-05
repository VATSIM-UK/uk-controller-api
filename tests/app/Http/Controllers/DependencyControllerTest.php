<?php
namespace App\Http\Controllers;

use App\BaseApiTestCase;
use Illuminate\Support\Facades\Storage;

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
                'uri' => '/one',
                'local_file' => 'one.json',
            ],
            [
                'key' => 'DEPENDENCY_TWO',
                'uri' => '/two',
                'local_file' => 'two.json',
            ]
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
