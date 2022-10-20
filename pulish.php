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

//路由键
$routeKey = "key_1";
//交换机名称
$exchangeName = "exchange_1";

//创建一个通道
$ch = new AMQPChannel($conn);
//创建一个交换机
$ex = new AMQPExchange($ch);

$ex->setName($exchangeName);

$ex->setType(AMQP_EX_TYPE_DIRECT);

$ex->setFlags(AMQP_DURABLE);

$ex->declareExchange();

for($i=0; $i<1000; ++$i){
    $message = [
        'data'  =>   '消息'.$i,
    ];
    $ex->publish(json_encode($message), $routeKey);
}