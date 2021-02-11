<?php

namespace Snovio\Amqp;

use PhpAmqpLib\Message\AMQPMessage;

class AmqpProducer
{
    /**
     * @var AmqpConnectionInterface
     */
    protected $AMQPConnection;

    /**
     * RabbitMQModel constructor.
     * @param AmqpConnectionInterface $AMQPConnection
     */
    public function __construct(AmqpConnectionInterface $AMQPConnection)
    {
        $this->AMQPConnection = $AMQPConnection;
    }

    /**
     * @param array $data
     * @param string $queue
     * @param int $priority
     * @throws \JsonException
     */
    public function publish(array $data, string $queue, int $priority = 0): void
    {
        $msgAttr = [];
        if ($priority !== 0) {
            $msgAttr['priority'] = $priority;
        }
        $msg = new AMQPMessage(json_encode($data, JSON_THROW_ON_ERROR), $msgAttr);

        $this->AMQPConnection->getChannel()->basic_publish(
            $msg,   #message
            '',     #exchange
            $queue  #routing key (queue)
        );
    }

    /**
     * @param string $queue
     * @return int
     */
    public function getQueuedMessagesCount(string $queue): int
    {
        $queueInfo = $this->AMQPConnection->getChannel()->queue_declare($queue, true);
        return $queueInfo[1] ?? 0;
    }
}
