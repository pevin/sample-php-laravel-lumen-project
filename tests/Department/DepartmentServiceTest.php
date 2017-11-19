<?php

namespace Tests;

use App\Department\DepartmentRequestService;
use App\Department\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DepartmentServiceTest extends TestCase
{
    public function testGet()
    {
        $expected = collect(['test' => 'test']);
        $mockRequestService = Mockery::mock(DepartmentRequestService::class);
        $mockRequestService
            ->shouldReceive('get')
            ->once()
            ->andReturn(new JsonResponse($expected->toJson()));
        $service = new DepartmentService($mockRequestService);
        $actual = $service->get(1);
        $this->assertEquals($expected->toArray(), $actual);
    }

    public function testGetWithError()
    {
        Log::shouldReceive('error')->once();
        $mockRequestService = Mockery::mock(DepartmentRequestService::class);
        $mockRequestService
            ->shouldReceive('get')
            ->andThrow(new HttpException(Response::HTTP_NOT_ACCEPTABLE));
        $service = new DepartmentService($mockRequestService);
        $actual = $service->get(1);
        $this->assertEmpty($actual);
    }

    public function testGetIds()
    {
        $expected = collect([1, 2, 3]);
        $mockRequestService = Mockery::mock(DepartmentRequestService::class);
        $mockRequestService
            ->shouldReceive('getIds')
            ->once()
            ->andReturn(new JsonResponse($expected->toJson()));
        $service = new DepartmentService($mockRequestService);
        $actual = $service->getIds();
        $this->assertEquals($expected->toArray(), $actual);
    }

    public function testGetIdsWithError()
    {
        Log::shouldReceive('error')->once();
        $mockRequestService = Mockery::mock(DepartmentRequestService::class);
        $mockRequestService
            ->shouldReceive('getIds')
            ->andThrow(new HttpException(Response::HTTP_NOT_ACCEPTABLE));
        $service = new DepartmentService($mockRequestService);
        $actual = $service->getIds();
        $this->assertEmpty($actual);
    }
}
