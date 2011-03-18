<?php

namespace MQ\Tests\Unit\Util\Timer;

use MQ\Util\Timer\WatchdogTimer;
use MQ\Exception\ExceptionTimeout;

/**
 *
 * @package MQ
 * @subpackage Test
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class WatchdogTimerTest extends \PHPUnit_Framework_TestCase
{
  /**
   *
   * @expectedException MQ\Exception\Exception
   */
  public function testConstructThrowsIfLimitNotValid()
  {
    new WatchdogTimer(-5);
  }

  public function testLimit()
  {
    $timer = new WatchdogTimer(5.5);

    $this->assertEquals(5.5, $timer->limit());
  }

  /**
   *
   * @expectedException MQ\Exception\Exception
   */
  public function testIsTimeoutThrowsIfCalledBeforeStart()
  {
    $timer = new WatchdogTimer(1);
    $timer->isTimeout();
  }

  public function testIsTimeoutShouldReturnFalseIfLimitNotReached()
  {
    $timer = new WatchdogTimer(5);

    $timer->start();

    $this->assertFalse($timer->isTimeout());
  }

  public function testIsTimeoutShouldReturnTrueIfLimitReached()
  {
    $timer = new WatchdogTimer(0.00001);

    $timer->start();

    $this->assertTrue($timer->isTimeout());
  }

  public function testThrowTimeoutReturnSelfInstance()
  {
    $timer = new WatchdogTimer(5);
    $timer->start();

    $this->assertSame($timer, $timer->throwTimeout());
  }

  /**
   *
   * @depends testIsTimeoutThrowsIfCalledBeforeStart
   * @depends testIsTimeoutShouldReturnFalseIfLimitNotReached
   * @depends testIsTimeoutShouldReturnTrueIfLimitReached
   */
  public function testThrowTimeoutShouldNotCallTimeoutCallback()
  {
    $isCalled = false;
    $timer = new WatchdogTimer(5, function() use(&$isCalled) {
      $isCalled = true;
    });

    $timer->start();

    $this->assertFalse($isCalled);
  }

  /**
   *
   * @depends testIsTimeoutThrowsIfCalledBeforeStart
   * @depends testIsTimeoutShouldReturnFalseIfLimitNotReached
   * @depends testIsTimeoutShouldReturnTrueIfLimitReached
   */
  public function testThrowTimeoutShouldCallTimeoutCallbackIfLimitNotReached()
  {
    $isCalled = false;
    $timer = new WatchdogTimer(0.00001, function() use(&$isCalled) {
      $isCalled = true;
    });

    $timer->start();

    try {
      $timer->throwTimeout();
    } catch (ExceptionTimeout $e) {
      $this->assertTrue($isCalled);
      return;
    }

    $this->fail('The exception is expected to be thrown');
  }

  public function testThrowTimeoutShouldCallTimeoutCallbackWithSelfInstanceAsParameter()
  {
    $testCase = $this;

    $timer = new WatchdogTimer(0.00001, function($timer) use($testCase) {
      $testCase->assertInstanceOf('MQ\Util\Timer\WatchdogTimer', $timer);
    });

    $timer->start();

    try {
      $timer->throwTimeout();
    } catch (ExceptionTimeout $e) {
      return;
    }

    $this->fail('The exception is expected to be thrown');
  }
}