<?php

namespace App\Rules\Sid;

use App\BaseFunctionalTestCase;
use App\Models\Runway\Runway;
use App\Models\Sid;
use Illuminate\Support\Facades\Validator;

class SidIdentifierMustBeUniqueForRunwayTest extends BaseFunctionalTestCase
{
    protected function validateResult(Runway $runway, ?Sid $existingSid, string $identifier)
    {
        return !Validator::make(
            [
                'identifier' => $identifier,
            ],
            [
                'identifier' => new SidIdentifiersMustBeUniqueForRunway($runway, $existingSid)
            ]
        )
            ->fails();
    }

    public function testItHasNoClashesNoExistingSid()
    {
        $this->assertTrue(
            $this->validateResult(
                Runway::find(1),
                null,
                'TEST1Y'
            )
        );
    }

    public function testItHasClashesNoExistingSid()
    {
        $this->assertFalse(
            $this->validateResult(
                Runway::find(1),
                null,
                'TEST1X'
            )
        );
    }

    public function testItHasNoClashesExistingSid()
    {
        $this->assertTrue(
            $this->validateResult(
                Runway::find(1),
                Sid::find(1),
                'TEST1Y'
            )
        );
    }

    public function testItHasNoClashesExistingSidResave()
    {
        $this->assertTrue(
            $this->validateResult(
                Runway::find(1),
                Sid::find(1),
                'TEST1X'
            )
        );
    }

    public function testItHasClashesExistingSid()
    {
        $this->assertFalse(
            $this->validateResult(
                Runway::find(1),
                Sid::find(2),
                'TEST1X'
            )
        );
    }
}
