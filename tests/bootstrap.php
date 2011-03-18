<?php

require_once __DIR__.'/../autoload.php';

//use MQ\Server\ServerActiveMQ;
//use MQ\Exception\Exception;
//
//if (!ServerActiveMQ::hasDefault()) {
//  throw new Exception('For functional tests it is requed to setup an ActiveMQ Server');
//}

//// ActiveMQ test server
//use MQ\Server\ActiveMQServer;
//ActiveMQServer::setDefault(new ActiveMQServer(__DIR__.'/server/bin/server'))->stop()->start();
//
//// Test server connection
//global $testActiveMQ;
//
//$testConnectionParamaters = array(
//  'host' =>     'localhost',
//  'port' =>     '61613',
//  'protocol' => 'tcp',
//  'username' => '',
//  'password' => '');