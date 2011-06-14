<?php

namespace MQ\Server;

use MQ\Exception\Exception;

/**
 *
 * @package MQ
 * @subpackage Server
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class ServerActiveMQ
{
  /**
   *
   * @var ActiveMQ
   */
  protected static $_default;

  /**
   *
   * @var string
   */
  protected $_bin;

  /**
   *
   * @var array
   */
  protected $_connectParameters = array();

  /**
   *
   * @param string $activeMqBin
   *
   * @throws Exception
   *
   * @return void
   */
  public function __construct($activeMqBin, array $connectParameters)
  {
    if (!(is_file($activeMqBin) && is_executable($activeMqBin))) {
      throw new Exception('Cannot start activemq because the bin file `'.$activeMqBin.'` does not exist or not excecutable');
    }

    $this->_bin = $activeMqBin;
    $this->_connectParameters = $connectParameters;
  }

  /**
   *
   * @return array
   */
  public function getConnectionParameters()
  {
    return $this->_connectParameters;
  }

//  public function isStarted()
//  {
//    $output = implode(' ', $this->_exec('status', false));
//
//    return strstr($output, 'ActiveMQ is running') !== false;
//  }

  /**
   *
   * @return ActiveMQServer
   */
  public function start()
  {
    $this->_exec('start');

    sleep(6);

    return $this;
  }

  /**
   *
   * @return ActiveMQServer
   */
  public function stop()
  {
    $this->_exec('stop');

    sleep(1);

    return $this;
  }

  /**
   *
   * @return ActiveMQServer
   */
  public function restart()
  {
    $this->_exec('restart');

    return $this;
  }

  /**
   *
   * @param string $destination
   *
   * @return ActiveMQServer
   */
  public function purge($destination)
  {
    $output = $this->_exec('status');

    $output = implode("\n", $output);

    $matches = array();
    if (!preg_match('/ActiveMQ is running \(pid \'(\d{1,6})\'\)/', $output, $matches)) {
      throw new Exception('The pid file of ActiveMQ server (bin file: `'.$this->_bin.'`) cannot be parsed. May be you forget to run activeMq server?');
    }
    list(, $pid) = $matches;

    $this->_exec("purge --pid {$pid} {$destination}");

    return $this;
  }

  /**
   *
   * @param string $cmd
   *
   * @throws Exception
   *
   * @return string
   */
  protected function _exec($cmd, $throwOnError = true)
  {
    $returnVar = 0;
    $output = '';
    $cmd = "{$this->_bin} {$cmd}";

    exec($cmd, $output, $returnVar);

    if ($throwOnError && $returnVar != 0) {
      throw new Exception('ActiveMQ broker (exec file: `'.$cmd.'`) was not finins commad `'.$cmd.'` due to some reasons. The exit code is `'.$returnVar.'`');
    }

    return $output;
  }

  /**
   *
   * @param ActiveMQServer $server
   *
   * @return ActiveMQServer
   */
  public static function setDefault(ActiveMQServer $server)
  {
    self::$_default = $server;

    return $server;
  }

  /**
   *
   * @return boolean
   */
  public static function hasDefault()
  {
    return (bool) self::$_default;
  }

  /**
   *
   * @return ActiveMQServer
   */
  public static function getDefault()
  {
    if (!self::hasDefault()) {
      throw new Exception('The default server was not set. It has to be done before any other use');
    }

    return self::$_default;
  }
}