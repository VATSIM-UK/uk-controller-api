<?php

namespace App\Helpers\User;

use App\BaseUnitTestCase;

class UserConfigCollectionTest extends BaseUnitTestCase
{

    /**
     * Collection under test
     *
     * @var UserConfigCollection
     */
    private $userConfigCollection;

    /**
     * Config tests
     *
     * @var array
     */
    private $configs;

    public function setUp()
    {
        parent::setUp();
        $this->configs = [
            new UserConfig('testkey1'),
            new UserConfig('testkey2'),
            new UserConfig('testkey3')
        ];
        $this->userConfigCollection = new UserConfigCollection(
            $this->configs[0],
            $this->configs[1],
            $this->configs[2]
        );
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(UserConfigCollection::class, $this->userConfigCollection);
    }

    public function testItSerializesToJson()
    {
        $this->assertEquals($this->configs, $this->userConfigCollection->jsonSerialize());
    }
}
