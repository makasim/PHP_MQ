<?php

namespace MQ\Util\Timer;

use MQ\Exception\Exception;
use MQ\Exception\ExceptionTimeout;

/**
 *
 * @package MQ
 * @subpackage Util
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class Timer
{
  /**
   *
   * @var float
   */
  protected $_started = false;

  /**
   *
   * @return Timer
   */
  public function start()
  {
    if (false !== $this->_started) {
      throw new Exception('The timer has been already started. It can be started only one time');
    }

    $this->_started = $this->current();

    return $this;
  }

  /**
   *
   * @throws Exception
   *
   * @return float microtime
   */
  public function elapsed()
  {
    $this->_throwIfNotStated();

    return $this->current() - $this->started();
  }

  /**
   *
   * @return float microtime
   */
  public function started()
  {
    $this->_throwIfNotStated();

    return $this->_started;
  }

  /**
   *
   * @return float microtime
   */
  public function current()
  {
    return microtime(true);
  }

  /**
   *
   * @throws Exception
   *
   * @return void
   */
  protected function _throwIfNotStated()
  {
    if (false === $this->_started) {
      throw new Exception('The timer has not been started yet');
    }
  }

  /**
   *
   * @param int|float $microtimeLimit
   *
   * @return Timer
   */
  public function throwLimitOver($microtimeLimit)
  {
    $format = function ($microtime) {
      list(,$micro) = explode('.', (string) $micro);

      return date('H:i:s ', $microtime).$micro;
    };

    $elapsed = $this->elapsed();
    if ($elapsed > $microtimeLimit) {
      throw new ExceptionTimeout('The limit is over. The timer is started at `'.$format($this->started()).'`.Elapsed time is `'.$format($elapsed).'`. Limit: `'.$format($microtimeLimit).'`');
    }

    return $this;
  }

  /**
   *
   * @return Timer
   */
  public static function create()
  {
    $class = get_called_class();

    return new $class;
  }

  /**
   *
   * @param float $microtime
   *
   * @return string
   */
  public static function formatHuman($microtime)
  {
    $time = date('H:i:s', $microtime);
    list(, $micro) = explode('.', (string) $microtime);

    return "{$time}.{$micro}";
  }

}