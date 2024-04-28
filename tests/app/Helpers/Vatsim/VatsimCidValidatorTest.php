<?php

namespace App\Helpers\Vatsim;

use App\BaseUnitTestCase;

class VatsimCidValidatorTest extends BaseUnitTestCase
{
    public function testItRejectsIdsThatAreTooLow()
    {
        $this->assertFalse(VatsimCidValidator::isValid(VatsimCidValidator::MINIMUM_CID - 1));
    }

    public function testItRejectsIdsBetweenFoundersAndMembersLower()
    {
        $this->assertFalse(VatsimCidValidator::isValid(VatsimCidValidator::MAXIMUM_FOUNDER_CID + 1));
    }

    public function testItRejectsIdsBetweenFoundersAndMembersUpper()
    {
        $this->assertFalse(VatsimCidValidator::isValid(VatsimCidValidator::MINIMUM_MEMBER_CID - 1));
    }

    public function testItAcceptsFounderCidsLowerBoundary()
    {
        $this->assertTrue(VatsimCidValidator::isValid(VatsimCidValidator::MINIMUM_CID));
    }

    public function testItAcceptsFounderCidsUpperBoundary()
    {
        $this->assertTrue(VatsimCidValidator::isValid(VatsimCidValidator::MAXIMUM_FOUNDER_CID));
    }

    public function testItAcceptsMemberCidsLowerBoundary()
    {
        $this->assertTrue(VatsimCidValidator::isValid(VatsimCidValidator::MINIMUM_MEMBER_CID));
    }

    public function testItAcceptsMemberCids()
    {
        $this->assertTrue(VatsimCidValidator::isValid(VatsimCidValidator::MINIMUM_MEMBER_CID + 10232));
    }
}
