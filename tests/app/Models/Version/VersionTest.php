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
        $version = Version::withTrashed()->find(1);
        $expected = [
            'id' => 1,
            'version' => '1.0.0',
            'plugin_release_channel_id' => 1,
            'created_at' => '2017-12-02T00:00:00.000000Z',
            'updated_at' => '2017-12-03T00:00:00.000000Z',
            'deleted_at' => '2017-12-04T00:00:00.000000Z',
        ];

        $this->assertEquals($expected, $version->jsonSerialize());
    }

    public function testItsAllowedStatusMayBeToggled()
    {
        $this->assertFalse(Version::find(3)->trashed());
        Version::find(3)->toggleAllowed();
        $this->assertTrue(Version::withTrashed()->find(3)->trashed());
        Version::withTrashed()->find(3)->toggleAllowed();
        $this->assertFalse(Version::find(3)->trashed());
    }
}
