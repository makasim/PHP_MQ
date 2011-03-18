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
class WatchdogTimer extends Timer
{
  /**
   *
   * @var float|int
   */
  protected $_limit;

  /**
   *
   * @var Closure
   */
  protected $_timeoutCallback;


  /**
   *
   * @param float|int $limit microtime
   * @param Closure $timoutCallback
   */
  public function __construct($limit, \Closure $timoutCallback = null)
  {
    $this->_timeoutCallback = $timoutCallback ?: function(WatchdogTimer $timer) {};

    if (!(is_numeric($limit) && $limit > 0)) {
      throw new Exception('The limit provided is not valid. Should be positive numeric value but it was given `'.var_export($limit, true).'`');
    }

    $this->_limit = $limit;
  }

  /**
   *
   * @return float
   */
  public function limit()
  {
    return $this->_limit;
  }

  public function isTimeout()
  {
    return $this->elapsed() > $this->limit();
  }

  /**
   *
   * @return WatchdogTimer
   */
  public function throwTimeout()
  {
    if ($this->isTimeout()) {
      $timeoutCallback = $this->_timeoutCallback;
      $timeoutCallback($this);

      throw new ExceptionTimeout('The limit is over. '.
        'The timer is started at `'.self::formatHuman($this->started()).'`.'.
        'Elapsed time is `'.self::formatHuman($this->elapsed()).'`. '.
        'Limit: `'.self::formatHuman($this->limit()).'`');
    }

    return $this;
  }
}