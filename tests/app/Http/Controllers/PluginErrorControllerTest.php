<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\PluginError\PluginError;

class PluginErrorControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(PluginErrorController::class, $this->app->make(PluginErrorController::class));
    }

    public function testItReturns400OnMissingUserReport()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'plugin-error',
            [
                'data' => [
                    'foo' => 'bar'
                ],
            ]
        )->seeStatusCode(400);
    }

    public function testItReturns400OnMissingData()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'plugin-error',
            [
                'user_report' => true,
            ]
        )->seeStatusCode(400);
    }

    public function testItReturnsNoContentOnSuccess()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'plugin-error',
            [
                'user_report' => true,
                'data' => [
                    'foo' => 'bar',
                    'baz' => [
                        'foo' => 'bar',
                    ]
                ],
            ]
        )->seeStatusCode(204);
    }

    public function testItStoresUserReports()
    {
        $data = [
            'baz' => [
                'foo' => 'bar',
            ],
            'foo' => 'bar',
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'plugin-error',
            [
                'user_report' => true,
                'data' => $data
            ]
        );

        $pluginError = PluginError::orderBy('created_at', 'desc')->first();
        $this->assertEquals(self::ACTIVE_USER_CID, $pluginError->user_id);
        $this->assertEquals(1, $pluginError->user_report);
        $this->assertEquals($data, json_decode($pluginError->data, true));
    }

    public function testItStoresNonUserReports()
    {
        $data = [
            'baz' => [
                'foo' => 'bar',
            ],
            'foo' => 'bar',
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'plugin-error',
            [
                'user_report' => false,
                'data' => $data
            ]
        );

        $pluginError = PluginError::orderBy('created_at', 'desc')->first();
        $this->assertEquals(self::ACTIVE_USER_CID, $pluginError->user_id);
        $this->assertEquals(0, $pluginError->user_report);
        $this->assertEquals($data, json_decode($pluginError->data, true));
    }
}
