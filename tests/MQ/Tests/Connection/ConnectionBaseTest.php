<?php

namespace MQ\Tests\Unit\Connection;

/**
 *
 * @package MQ
 * @subpackage Test
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class ConnectionBaseTest extends \PHPUnit_Framework_TestCase
{
  public function testConstructAcceptParameters()
  {
    $expectedParameters = array(
      'host'     => 'example.com',
      'port'     => 66633,
      'protocol' => 'tcp',
      'username' => '',
      'password' => '');

    $connectionClass = $this->getMockClass('MQ\Connection\ConnectionBase');
    $connection = new $connectionClass($expectedParameters);

    $this->assertAttributeEquals($expectedParameters, '_parameters', $connection);
  }

  /**
   *
   * @depends testConstructAcceptParameters
   */
  public function testConstructFilterNotSupportedParameters()
  {
    $expectedParameters = array(
      'host'     => 'example.com',
      'port'     => 66633,
      'protocol' => 'tcp',
      'username' => '',
      'password' => '');

    $parameters = $expectedParameters;
    $parameters['foo'] = 'foo';
    $parameters['bar'] = 'bar';

    $connectionClass = $this->getMockClass('MQ\Connection\ConnectionBase');
    $connection = new $connectionClass($parameters);

    $this->assertAttributeEquals($expectedParameters, '_parameters', $connection);
  }

  /**
   *
   * @depends testConstructAcceptParameters
   *
   * @expectedException MQ\Exception\Exception
   */
  public function testConstructThrowsIfARequiredParameterMissed()
  {
    $expectedParameters = array(
      //'host'     => 'example.com',
      'port'     => 66633,
      'protocol' => 'tcp',
      'username' => '',
      'password' => '');

    $connectionClass = $this->getMockClass('MQ\Connection\ConnectionBase');
    $connection = new $connectionClass($expectedParameters);
  }
}