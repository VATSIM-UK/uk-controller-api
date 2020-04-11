<?php

namespace App\Models\Version;

use App\BaseFunctionalTestCase;

class VersionTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $version = new Version();
        $this->assertInstanceOf(Version::class, $version);
    }

    public function testItSerializesToJson()
    {
        $version = Version::find(1);
        $expected = [
            'id' => 1,
            'version' => '1.0.0',
            'created_at' => '2017-12-02T00:00:00.000000Z',
            'updated_at' => '2017-12-03T00:00:00.000000Z',
            'allowed' => false,
        ];

        $this->assertEquals($expected, $version->jsonSerialize());
    }

    public function testItHasAnAllowedAttribute()
    {
        $this->assertTrue(Version::find(3)->allowed);
    }

    public function testItsAllowedStatusMayBeToggled()
    {
        $this->assertTrue(Version::find(3)->allowed);
        Version::find(3)->toggleAllowed();
        $this->assertFalse(Version::find(3)->allowed);
        Version::find(3)->toggleAllowed();
        $this->assertTrue(Version::find(3)->allowed);
    }
}
