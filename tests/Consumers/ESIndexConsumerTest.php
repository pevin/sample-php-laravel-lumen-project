<?php

namespace Tests\Consumers;

use App\Consumers\ESIndexConsumer;
use App\Employee\EmployeeESIndexService;
use App\ES\ESIndexServiceException;
use App\ES\ESIndexServiceFactory;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery as m;

class ESIndexConsumerTest extends TestCase
{
    public function testCallback()
    {
        $message = new \stdClass();
        $message->body = base64_encode(json_encode([
            'type' => 'test type',
        ]));

        $fakeResolver = new class() {
            public function acknowledge($a)
            {
                return $a;
            }
        };

        $esIndexService = m::mock(EmployeeESIndexService::class);
        $esIndexService
            ->shouldReceive('process')
            ->once();
        $factory = m::mock(ESIndexServiceFactory::class);
        $factory
            ->shouldReceive('createService')
            ->once()
            ->andReturn($esIndexService);

        $consumer = new ESIndexConsumer($factory);

        $consumer->callback($message, $fakeResolver);
    }

    public function testCallbackWithEmptyType()
    {
        $message = new \stdClass();
        $message->body = base64_encode(json_encode([
            'test' => 'test type',
        ]));

        $fakeResolver = new class() {
            public function acknowledge($a)
            {
                return $a;
            }
        };

        $factory = m::mock(ESIndexServiceFactory::class);

        Log::shouldReceive('error')
            ->once();

        $consumer = new ESIndexConsumer($factory);
        $consumer->callback($message, $fakeResolver);
    }

    public function testCallbackInvalidDetails()
    {
        $message = new \stdClass();
        $message->body = base64_encode(json_encode([
            'type' => 'test type',
        ]));

        $fakeResolver = new class() {
            public function acknowledge($a)
            {
                return $a;
            }
        };

        $factory = m::mock(ESIndexServiceFactory::class);
        $factory
            ->shouldReceive('createService')
            ->once()
            ->andThrow(ESIndexServiceException::class);

        Log::shouldReceive('error')
            ->once();

        $consumer = new ESIndexConsumer($factory);
        $consumer->callback($message, $fakeResolver);
    }
}
