<?php

namespace MQ\Connection;

/**
 *
 * @package MQ
 * @subpackage Connection
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 * @method ConnectionConsumer consumer
 * @method ConnectionProducer producer
 *
 */
class ConnectionRepository
{
  protected $_connections = array();

  /**
   *
   * @var ConnectionFactory
   */
  protected $_connectionFactory;

  public function __construct(ConnectionFactory $connectionFactory)
  {
    $this->_connectionFactory = $connectionFactory;
  }

  public function __call($name, $args)
  {
    if (!isset($this->_connections[$name])) {
      $name = ucfirst($name);

      $this->_connections[$name] = call_user_func_array(array($this->_connectionFactory, "create{$name}"), $args);
    }

    return $this->_connections[$name];
  }
}
