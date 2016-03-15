<?php

require_once __DIR__ . '/vendor/autoload.php';

include 'config/config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(
        $host = $config['host'],
        $port = $config['port'],
        $user = $config['user'],
        $password = $config['password'],
        $vhost = '/',
        $insist = false,
//        $insist = true,
        $login_method = 'AMQPLAIN',
        $login_response = null,
        $locale = 'en_US',
        $connection_timeout = 3,
//        $connection_timeout = 60,
        $read_write_timeout = 3,
//        $read_write_timeout = 60,
        $context = null,
//        $keepalive = false,
        $keepalive = true,
        $heartbeat = 0);
//        $heartbeat = 60);

$channel = $connection->channel();

$channel->queue_declare('hallo', false, false, false, false);

$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hallo');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();