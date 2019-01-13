<?php

namespace App\Helpers\User;

use App\BaseUnitTestCase;

class UserConfigTest extends BaseUnitTestCase
{
    /**
     * Class under test
     *
     * @var UserConfig
     */
    private $userConfig;

    public function setUp()
    {
        parent::setUp();
        $this->userConfig = new UserConfig('user-api-key');
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserConfig::class, $this->userConfig);
    }

    public function testItHasApiKey()
    {
        $this->assertEquals('user-api-key', $this->userConfig->apiKey());
    }

    public function testItHasAnApiUrl()
    {
        $this->assertEquals(env('APP_URL'), $this->userConfig->apiUrl());
    }

    public function testItSerializesToJson()
    {
        $expected = [
            'api-url' => env('APP_URL'),
            'api-key' => 'user-api-key',
        ];

        $this->assertEquals($expected, $this->userConfig->jsonSerialize());
    }
}
