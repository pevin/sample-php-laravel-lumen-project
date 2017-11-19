<?php

namespace Tests\ES;

use App\ES\ESIndexQueueService;
use Bschmitt\Amqp\Facades\Amqp;

class ESIndexQueueServiceTest extends \Tests\TestCase
{
    public function testQueue()
    {
        $details = [
            'id' => [1],
            'type' => 'employee',
        ];

        $indexQueueService = new ESIndexQueueService();

        Amqp::shouldReceive('publish')
            ->once();

        $indexQueueService->queue($details);
    }
}
