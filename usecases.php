<?php

#### Frontend

$client = new Client($userId, $connectionParameters);

$msg = $client->createMessage('some content');

$msgAnswer = $client->channels()->queue('property.create')
  ->send($msg)
  ->awaitAnswerTo($msg, $timeout = 5);


echo $msgAnswer->getContent();

#### Backend

$client = new Client($id, $connectionParameters);

$client->serviceFinder()
 ->addDirectory($dir)
 ->find()
 ->subscribeTo($client->connections()->consumer());

$client->listen();


#### Connection Managing

  // simple creation
    // simple connectin just send recive methods
    $connSimple = new ConnectionSimple(array('host' => 'example.com', 'port' => '666'));

    // subscribe, unsubscribe and listen methods
    $connConsumer = new ConnectionConsumer(array('host' => 'example.com', 'port' => '666'));

    // answerTo methods
    $connProducer = new ConnectionProducer(array('host' => 'example.com', 'port' => '666'));

  // ConnectionFactory. alwways creates new connections
    $factory = new ConnectionFactory(array('host' => 'example.com', 'port' => '666'));

    $connConsumer = $connectionFactory->simple();

    $connConsumer = $connectionFactory->consumer();
    $connConsumer = $connectionFactory->consumer(null, array('port' => '777'));
    $connConsumer = $connectionFactory->consumer('default', array('port' => '777'));
    $connConsumer = $connectionFactory->consumer('foo', array('port' => '777'));

    $connProducer = $connectionFactory->producer();

  // ConnectionRepository
    $repo = new ConnectionRepository(new ConnectionFactory(array('host' => 'example.com', 'port' => '666')));
    $repo = new ConnectionRepository(new ConnectionFactory(array('host' => 'example.com', 'port' => '666')), 'repoName');

    $connConsumer = $repo->consumer();
    $connProducer = $repo->producer();

#### Send simple mesage

  // sync
    $conn = new ConnectionSimple();

    $msg = new Message('bar');
    $msg->setSync(true);

    $conn->send($msg);

  //async
    $conn = new ConnectionSimple();

    $msg = new Message('bar');
    $msg->setSync(false);

    $conn->send($msg);

#### Send a message

    // simple way
    $conn = new ConnectionSimple();

    $msg = new Message('bar');
    $msg->setReplayTo('replay-to-queue');

    $conn->send($msg);
    while (!$answerMsg) {
      $answerMsg = $conn->answerTo($msg);
      sleep(1);
    }

    // using Client
    $connProducer = new ConnectionProducer();

    $client = new Client($clientUnitId);

    $msg = new Message('bar');
    $msg->setReplayTo($client->getReplayToQueue());
    $connProducer->send($msg);
    while (!$answerMsg) {
      $answerMsg = $connProducer->answerTo($msg);
      sleep(1);
    }

#### Recive message

  // Simple
    $conn = new ConnectionProducer();
    $msg = $conn->recive('foo-queue');

  // Queue or Topic
    $conn = new ConnectionProducer();
    $msg = $conn->recive(new Queue('foo-queue'));
    $msg = $conn->recive(new Topic('foo-topic'));

#### Demons

  // subscription (first variant)
    $conn = new ConnectionConsumer();
    $conn->subscribe('queue-foo', function(Message $msg) {
      //handle method
    });
    $conn->subscribe(new Queue('queue-foo'), array($service, 'handleFoo'));
    $conn->subscribe(new Queue('queue-foo'), new Subscriber(array($service, 'handleFoo')));

  // unsubscribption

    $conn = new ConnectionConsumer();

    $handler = function(Message $msg) {
      //handle method
    };

    $conn->subscribe('foo', $handler);
    $conn->unsubscribe('foo', $handler);

  // listen for events

    $conn = new ConnectionConsumer();

    $conn->subscribe(new Queue('queue-foo'), array($serviceOne, 'handleFoo'));
    $conn->subscribe(new Queue('queue-foo'), array($serviceSecond, 'handleFoo'));
    $conn->subscribe(new Queue('queue-bar'), array($serviceThird, 'handleBar'));

    while (true) {
      $conn->listen();
    }

  // forking subprocess (First variant)
    $conn = new ConnectionConsumer();

    $subscriber = Subscrber(array($serviceOne, 'handleFoo'));
    $subscriber->setFork(true);

    $conn->subscribe(new Queue('queue-foo'), $subscriber);

    while (true) {
      $conn->listen();
    }

  // forking subprocess (Second variant)
    $conn = new ConnectionConsumer();

    $subscriber = SubscrberForkable(array($serviceOne, 'handleFoo'));

    $conn->subscribe(new Queue('queue-foo'), $subscriber);

    while (true) {
      $conn->listen();
    }

  // forking subprocess (Third variant)
    $conn = new ConnectionConsumer();

    $conn->setCallFunction(function($callback, $args) {
      //do fork

      // call function
    });

    $conn->subscribe(new Queue('queue-foo'), array($serviceOne, 'handleFoo'));

    while (true) {
      $conn->listen();
    }

#### Service searcher
  //based on directory and doc bloc
    class FooService
    {
      /**
       *
       * @queue foo
       * @queue bar
       * @topic bar
       */
      public function foo()
      {

      }
    }

    $serviceFinder = new ServiceFinder();
    $serviceFinder->addDirectory($serviceDir);
    $serviceFinder->find();

    $connectionConsumer = new ConnectionConsumer();
    foreach ($serviceFinder->get() as $channel => $callable) {
      $connectionConsumer->subscribe($channel, $callable);
    }

    // or
    $serviceFinder->subscribeTo($connectionConsumer);

    while (true) {
      $conn->listen();
    }

  // based on config

    /* yml:
     *
     * queue:
     *  foo:
     *    - [FooService. foo]
     *    - [BarService. bar]
     * topic:
     * - [FooService. foo]
     *
     *
     */

    $serviceFinder = new ServiceFinder();
    $serviceFinder->addConfig($ymlConfig);
    $serviceFinder->find();

    $connectionConsumer = new ConnectionConsumer();
    foreach ($serviceFinder->get() as $channel => $callable) {
      $connectionConsumer->subscribe($channel, $callable);
    }

    // or
    $serviceFinder->subscribeTo($connectionConsumer);

    while (true) {
      $conn->listen();
    }

#### Logging by example of Connection class

  // first variant
  $conn =  ConnectionSimple(array(), new FileLogger($path));

  // second variant
  $conn =  ConnectionSimple();
  $conn->setLogger(new FileLogger($path));

  // third variant
  $dispather = new EventDispather();

  $logger = new FileLogger($path);
  $dispather->subscribe('log', array($logger, 'log'));

  $conn =  ConnectionSimple(array(), $dispather);

  // disable logging
  $logger = new NullLogger();
  $conn =  ConnectionSimple(array(), $logger);

#### pre and post functions

  // decorators
    $service = new MonitoringServiceDecorator(new FooService());

    $connection = new ConnectionConsumer();
    $connection->subscribe('foo', array($service, 'foo'));

  // events
    $dispatcher = new EventDispatcher();

    $dispatcher->subscribe('consumer.onMessage.pre', function() {/** do stuff before service all */});
    $dispatcher->subscribe('consumer.onMessage.post', function() {/** do stuff before service all */});

    $connection = new ConnectionConsumer(array(), $dispatcher);

    while (true) {
     $conn->listen();
    }

#### Testing server

  $server = new ActiveMQServer($pathToActiveMQBin);

  $server->stop()->start();
  // or
  $server->restart();

  // clean up the queue.

  $server->purge();