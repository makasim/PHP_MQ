<?php

namespace MQ\Connection;

use MQ\Exception\Exception;
use MQ\Message\Message;

/**
 *
 * @package MQ
 * @subpackage Connection
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
abstract class ConnectionBase
{
  /**
   *
   * @var array
   */
  protected $_parameters = array(
    'host'     => null,
    'port'     => null,
    'protocol' => null,
    'username' => null,
    'password' => null);

  /**
   *
   * @var Stomp
   */
  protected $_stomp;

  /**
   *
   * @param array $parameters
   *
   * @throws Exception if a parameter is missed
   * @throws Exception if a parameter is empty
   *
   * @return void
   */
  public function __construct(array $parameters = array())
  {
    $parametersFiltered = array_intersect_key($parameters, $this->_parameters);
    if (count($parametersFiltered) !== count($this->_parameters)) {
      $diff = array_diff_key($this->_parameters, $parametersFiltered);
      throw new Exception('Some parameters (`'.implode('`, `', array_keys($diff)).'`) are missed but they are required.');
    }

    $this->_parameters = $parametersFiltered;
  }

  /**
   *
   * @return array
   */
  public function getParameters()
  {
    return $this->_parameters;
  }
}