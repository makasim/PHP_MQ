<?php

require_once __DIR__.'/src/vendor/Symfony2/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'MQ' => __DIR__.'/src',
));

$loader->register();

require_once __DIR__.'/src/vendor/Stomp/fuse/Stomp.php';
require_once __DIR__.'/src/vendor/Stomp/fuse/Stomp/Message.php';