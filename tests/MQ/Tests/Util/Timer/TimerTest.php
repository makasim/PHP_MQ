<?php

namespace MQ\Tests\Unit\Util\Timer;

use MQ\Util\Timer\Timer;

/**
 *
 * @package MQ
 * @subpackage Test
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class TimerTest extends \PHPUnit_Framework_TestCase
{
  public function testCreate()
  {
    $timerFirst = Timer::create();
    $this->assertInstanceOf('MQ\Util\Timer\Timer', $timerFirst);

    $timerSecond = Timer::create();
    $this->assertInstanceOf('MQ\Util\Timer\Timer', $timerSecond);

    $this->assertNotSame($timerFirst, $timerSecond);
  }

  public function testFormatHuman()
  {
    $formatedMicrotime = Timer::formatHuman('1300447804.180');
    $this->assertEquals('13:30:04.18', $formatedMicrotime);

    $formatedMicrotime = Timer::formatHuman('1300447804.18031');
    $this->assertEquals('13:30:04.18', $formatedMicrotime);

    $formatedMicrotime = Timer::formatHuman('1300447804.1808');
    $this->assertEquals('13:30:04.181', $formatedMicrotime);
  }

  public function testCurrent()
  {
    $timer = new Timer();

    $microtimeFirst = $timer->current();
    $this->assertInternalType('float', $microtimeFirst);

    $microtimeSecond = $timer->current();
    $this->assertInternalType('float', $microtimeSecond);

    $this->assertGreaterThan($microtimeFirst, $microtimeSecond);
  }

  public function testStartShouldReturnSelfInstance()
  {
    $timer = new Timer();

    $this->assertSame($timer, $timer->start());
  }

  /**
   *
   * @depends testStartShouldReturnSelfInstance
   *
   * @expectedException MQ\Exception\Exception
   */
  public function testStartThrowsIfCalledSecondTime()
  {
    $timer = new Timer();

    $timer->start()->start();
  }

  /**
   *
   * @expectedException MQ\Exception\Exception
   */
  public function testStartedThrowsIfCalledBeforeStart()
  {
    $timer = new Timer();
    $timer->started();
  }

  public function testStarted()
  {
    $beforeMicrotime = microtime();

    $timer = new Timer();
    $timer->start();

    $microtime = $timer->started();

    $this->assertInternalType('float', $microtime);
    $this->assertGreaterThan($beforeMicrotime, $microtime);
  }

  public function testElapsed()
  {
    $timer = new Timer();
    $timer->start();

    $microtimeFirst = $timer->elapsed();
    $this->assertInternalType('float', $microtimeFirst);
    $this->assertLessThan($timer->started(), $microtimeFirst);

    $microtimeSecond = $timer->elapsed();
    $this->assertInternalType('float', $microtimeSecond);
    $this->assertLessThan($timer->started(), $microtimeSecond);

    $this->assertGreaterThan($microtimeFirst, $microtimeSecond);
  }
}