<?php

namespace BotMan\BotMan\tests;

use BotMan\BotMan\Http\Curl;
use PHPUnit_Framework_TestCase;
use BotMan\BotMan\Drivers\NullDriver;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Tests\Fixtures\TestDriver;

class DriverManagerTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        DriverManager::unloadDriver(TestDriver::class);
    }

    /** @test */
    public function it_can_be_created()
    {
        $driverManager = new DriverManager([], new Curl());
        $this->assertInstanceOf(DriverManager::class, $driverManager);
    }

    /** @test */
    public function it_can_get_available_drivers()
    {
        $this->assertTrue(is_array(DriverManager::getAvailableDrivers()));
    }

    /** @test */
    public function it_can_load_custom_drivers()
    {
        $count = count(DriverManager::getAvailableDrivers());
        DriverManager::loadDriver(TestDriver::class);
        $this->assertSame($count + 1, count(DriverManager::getAvailableDrivers()));
    }

    /** @test */
    public function it_loads_custom_drivers_first()
    {
        DriverManager::loadDriver(TestDriver::class);
        $available = DriverManager::getAvailableDrivers();

        $this->assertSame(TestDriver::class, $available[0]);
    }

    /** @test */
    public function it_can_load_custom_drivers_from_name()
    {
        DriverManager::loadDriver(TestDriver::class);
        $this->assertInstanceOf(TestDriver::class, DriverManager::loadFromName('Test', []));
        $this->assertInstanceOf(TestDriver::class, DriverManager::loadFromName(TestDriver::class, []));
    }

    /** @test */
    public function it_can_find_a_driver_by_name()
    {
        $this->assertInstanceOf(NullDriver::class, DriverManager::loadFromName('foo', []));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_can_get_configured_drivers()
    {
        $this->assertCount(0, DriverManager::getConfiguredDrivers([]));
    }
}
