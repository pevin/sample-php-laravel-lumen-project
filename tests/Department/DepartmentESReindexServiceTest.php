<?php

namespace Tests\Department;

use App\Department\DepartmentESReindexService;
use App\Department\DepartmentService;
use App\ES\ESIndexQueueService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery as m;

class DepartmentESReindexServiceTest extends TestCase
{
    public function testReindex()
    {
        $requestService = m::mock(DepartmentService::class);
        $requestService
            ->shouldReceive('getIds')
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
        $service = new DepartmentESReindexService(
            $requestService,
            $queueService
        );

        $service->reindex();
    }
}
