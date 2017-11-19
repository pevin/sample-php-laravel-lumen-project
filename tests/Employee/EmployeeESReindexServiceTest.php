<?php

namespace Tests\Employee;

use App\Employee\EmployeeESReindexService;
use App\Employee\EmployeeRequestService;
use App\ES\ESIndexQueueService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery as m;

class EmployeeESReindexServiceTest extends TestCase
{
    public function testReindex()
    {
        $requestService = m::mock(EmployeeRequestService::class);
        $requestService
            ->shouldReceive('getEmployeeIds')
            ->once()
            ->andReturn(
                new JsonResponse(json_encode([
                    1,2,3
                ]))
            );
        $queueService = m::mock(ESIndexQueueService::class);
        $queueService
            ->shouldReceive('queue')
            ->once();
        $service = new EmployeeESReindexService(
            $requestService,
            $queueService
        );

        $service->reindex();
    }
}
