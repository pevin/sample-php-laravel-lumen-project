<?php

namespace Tests;

use App\User\UserRequestService;
use App\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserServiceTest extends TestCase
{
    public function testGet()
    {
        $expected = collect(['test' => 'test']);
        $mockRequestService = Mockery::mock(UserRequestService::class);
        $mockRequestService
            ->shouldReceive('get')
            ->once()
            ->andReturn(new JsonResponse($expected->toJson()));
        $service = new UserService($mockRequestService);
        $actual = $service->get(1);
        $this->assertEquals($expected->toArray(), $actual);
    }

    public function testGetWithError()
    {
        Log::shouldReceive('error')->once();
        $mockRequestService = Mockery::mock(UserRequestService::class);
        $mockRequestService
            ->shouldReceive('get')
            ->andThrow(new HttpException(Response::HTTP_NOT_ACCEPTABLE));
        $service = new UserService($mockRequestService);
        $actual = $service->get(1);
        $this->assertEmpty($actual);
    }
}
