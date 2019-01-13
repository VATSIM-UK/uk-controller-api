<?php

namespace App\Log;

use App\BaseUnitTestCase;
use Psr\Log\LoggerInterface;
use Monolog\Handler\NullHandler;

class NullLoggerFactoryTest extends BaseUnitTestCase
{
    /**
     * Factory under test
     *
     * @var NullLoggerFactory
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new NullLoggerFactory;
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(NullLoggerFactory::class, $this->factory);
    }

    public function testItReturnsAPsr7Logger()
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->factory->__invoke(['name' => 'logger']));
    }

    public function testTheLoggerHasAName()
    {
        $this->assertEquals('testlogger', $this->factory->__invoke(['name' => 'testlogger'])->getName());
    }

    public function testItHasANullLogger()
    {
        $this->assertInstanceOf(NullHandler::class, $this->factory->__invoke(['name' => 'testlogger'])->popHandler());
    }
}
