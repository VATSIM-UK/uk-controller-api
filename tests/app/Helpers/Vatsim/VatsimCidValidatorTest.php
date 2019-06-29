<?php

namespace App\Helpers\Vatsim;

use App\BaseUnitTestCase;

class VatsimCidValidatorTest extends BaseUnitTestCase
{
    /**
     * Validator to test
     *
     * @var VatsimCidValidator
     */
    private $validator;

    public function setUp() : void
    {
        parent::setUp();
        $this->validator = $this->app->make(VatsimCidValidator::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(VatsimCidValidator::class, $this->validator);
    }

    public function testItRejectsIdsThatAreTooLow()
    {
        $this->assertFalse($this->validator->isValid(VatsimCidValidator::MINIMUM_CID - 1));
    }

    public function testItRejectsIdsBetweenFoundersAndMembersLower()
    {
        $this->assertFalse($this->validator->isValid(VatsimCidValidator::MAXIMUM_FOUNDER_CID + 1));
    }

    public function testItRejectsIdsBetweenFoundersAndMembersUpper()
    {
        $this->assertFalse($this->validator->isValid(VatsimCidValidator::MINIMUM_MEMBER_CID - 1));
    }

    public function testItAcceptsFounderCidsLowerBoundary()
    {
        $this->assertTrue($this->validator->isValid(VatsimCidValidator::MINIMUM_CID));
    }

    public function testItAcceptsFounderCidsUpperBoundary()
    {
        $this->assertTrue($this->validator->isValid(VatsimCidValidator::MAXIMUM_FOUNDER_CID));
    }

    public function testItAcceptsMemberCidsLowerBoundary()
    {
        $this->assertTrue($this->validator->isValid(VatsimCidValidator::MINIMUM_MEMBER_CID));
    }

    public function testItAcceptsMemberCids()
    {
        $this->assertTrue($this->validator->isValid(VatsimCidValidator::MINIMUM_MEMBER_CID + 10232));
    }
}
