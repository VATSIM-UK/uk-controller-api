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

    public function setUp() : void
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
        $this->assertEquals(config('app.url'), $this->userConfig->apiUrl());
    }

    public function testItSerializesToJson()
    {
        $expected = [
            'api-url' => config('app.url'),
            'api-key' => 'user-api-key',
        ];

        $this->assertEquals($expected, $this->userConfig->jsonSerialize());
    }
}
