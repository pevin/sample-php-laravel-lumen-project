<?php

namespace App\ES;

use Bschmitt\Amqp\Message;
use Bschmitt\Amqp\Facades\Amqp;

class ESIndexQueueService
{
    /**
     * Queue name
     * @var string
     */
    private $queue;

    public function __construct()
    {
        $this->queue = env('ES_INDEX_QUEUE');
    }

    /**
     * Queue for indexing to ES
     * @param array $details Message body
     * @return void
     */
    public function queue(array $details)
    {
        $message = new Message(
            base64_encode(json_encode($details)),
            [
                'content_type' => 'application/json',
                'delivery_mode' => 1
            ]
        );

        Amqp::publish($this->queue, $message);
    }
}
