<?php
$config = [
    'host'      => '127.0.0.1',
    'vhost'     => '/',
    'port'      => 5672,
    'login'     => 'root',
    'password'  => '123456'
];

//链接broker
$conn = new AMQPConnection($config);
if(!$conn->connect()) {
    echo "cannot connect to broker.";
    exit();
}

//创建一个通道
$ch = new AMQPChannel($conn);
//创建一个交换机
$ex = new AMQPExchange($ch);
//路由键
$routeKey = "key_1";
//交换机名称
$exchangeName = "exchange_1";
//设置交换机名称
$ex->setName($exchangeName);
//设置交换机类型
$ex->setType(AMQP_EX_TYPE_DIRECT);
//设置持久化
$ex->setFlags(AMQP_DURABLE);
//声明交换机
$ex->declareExchange();

//创建一个消息队列
$q = new AMQPQueue($ch);
//设置队列名称
$q->setName('queue_1');
//设置队列持久
$q->setFlags(AMQP_DURABLE);
//声明消息队列
$q->declareQueue();

//交换机和队列通过routeKey进行绑定
$q->bind($ex->getName(), $routeKey);

//接收消息并进行处理的回调方法
function receive($envelope, $queue) {
    echo $envelope->getBody()."\n";
    $queue->ack($envelope->getDeliveryTag());
}

//设置队列消费者回调方法，并阻塞
$q->consume('receive');
