<?php

namespace App\Http\Controllers\Plugin;

use App\BaseApiTestCase;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

class PluginLogControllerTest extends BaseApiTestCase
{
    #[DataProvider('createSuccessProvider')]
    public function testItCreatesLogs(array $data): void
    {
        $response = $this->makeAuthenticatedApiRequest(parent::METHOD_POST, 'plugin/logs', $data);

        // Check structure
        $response->assertStatus(201);
        $response->assertJsonStructure(['id']);

        // Check created model
        $expectedData = $data;
        if (!isset($expectedData['metadata'])) {
            $expectedData['metadata'] = null;
        } else {
            $expectedData['metadata'] = DB::raw(sprintf('CAST(\'%s\' AS JSON)', $expectedData['metadata']));
        }

        $expectedData += ['id' => $response->json('id')];

        $this->assertDatabaseHas('plugin_logs', $expectedData);
    }

    public function createSuccessProvider()
    {
        return [
            'minimal' => [
                ['type' => 'foo', 'message' => 'bar'],
            ],
            'with data' => [
                ['type' => 'foo', 'message' => 'bar', 'metadata' => json_encode(['baz' => 'qux'])],
            ],
            'with null data' => [
                ['type' => 'foo', 'message' => 'bar', 'metadata' => null],
            ],
        ];
    }

    #[DataProvider('createFailureProvider')]
    public function testItRejectsBadLogs(array $data)
    {
        $this->makeAuthenticatedApiRequest(parent::METHOD_POST, 'plugin/logs', $data)
            ->assertStatus(422);
    }

    public function createFailureProvider()
    {
        return [
            'missing type' => [
                ['message' => 'bar'],
            ],
            'type not string' => [
                ['type' => 123, 'message' => 'bar'],
            ],
            'type longer than 65535 chars' => [
                ['type' => str_repeat('a', 65536), 'message' => 'bar'],
            ],
            'missing message' => [
                ['type' => 'foo'],
            ],
            'message not string' => [
                ['type' => 'foo', 'message' => 123],
            ],
            'message longer than 65535 chars' => [
                ['type' => 'foo', 'message' => str_repeat('a', 65536)],
            ],
            'invalid metadata' => [
                ['type' => 'foo', 'message' => 'bar', 'metadata' => 'not json'],
            ],
        ];
    }
}
