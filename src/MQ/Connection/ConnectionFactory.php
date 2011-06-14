<?php

namespace MQ\Connection;

/**
 *
 * @package MQ
 * @subpackage Connection
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class ConnectionFactory
{
  /**
   *
   * @var array
   */
  protected $_defaultConnectionParameters = array();

  /**
   *
   * @param array $defaultConnectionParameters
   *
   * @return void
   */
  public function __construct(array $defaultConnectionParameters = array())
  {
    $this->_defaultConnectionParameters = $defaultConnectionParameters;
  }

  /**
   *
   * @param array $connectionParameters
   *
   * @return ConnectionConsumer
   */
  public function createConsumer(array $connectionParameters = array())
  {
    $connectionParameters = array_merge($this->_defaultConnectionParameters, $connectionParameters);

    return new ConnectionConsumer($connectionParameters);
  }

  /**
   *
   * @param array $connectionParameters
   *
   * @return ConnectionProducer
   */
  public function createProducer(array $connectionParameters = array())
  {
    $connectionParameters = array_merge($this->_defaultConnectionParameters, $connectionParameters);

    return new ConnectionProducer($connectionParameters);
  }
}
