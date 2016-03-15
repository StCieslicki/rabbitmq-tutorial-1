<?php

require_once __DIR__ . '/vendor/autoload.php';

include 'config/config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

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

$channel->queue_declare('task_queue', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL-C', "\n";

$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
