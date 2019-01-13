<?php
namespace App\Providers;

use App\BaseUnitTestCase;
use App\Libraries\SquawkValidator;

class SquawkValidatorTest extends BaseUnitTestCase
{
    public function testItRejectsNonOctalSquawks()
    {
        $this->assertFalse(SquawkValidator::isValidSquawk("7888"));
    }

    public function testItRejectsReservedSquawks()
    {
        $this->assertFalse(SquawkValidator::isValidSquawk("7000"));
        $this->assertFalse(SquawkValidator::isValidSquawk("2200"));
    }

    public function testItAcceptsValidSquawks()
    {
        $this->assertTrue(SquawkValidator::isValidSquawk("3754"));
        $this->assertTrue(SquawkValidator::isValidSquawk("0754"));
    }
}
