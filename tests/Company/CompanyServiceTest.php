<?php

namespace Tests\Company;

use App\Company\CompanyRequestService;
use App\Company\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CompanyServiceTest extends TestCase
{
    public function testGetCompany()
    {
        $expected = collect(['id' => 1]);
        $mockRequestService = Mockery::mock(CompanyRequestService::class);
        $mockRequestService
            ->shouldReceive('get')
            ->andReturn(new JsonResponse($expected->toJson()));
        $service = new CompanyService($mockRequestService);
        $actual = $service->get($expected['id']);
        $this->assertEquals($expected['id'], $actual['id']);
    }

    public function testGetCompanyWithError()
    {
        Log::shouldReceive('error')->once();
        $mockRequestService = Mockery::mock(CompanyRequestService::class);
        $mockRequestService
            ->shouldReceive('get')
            ->andThrow(new HttpException(Response::HTTP_NOT_ACCEPTABLE));
        $service = new CompanyService($mockRequestService);
        $actual = $service->get(1);
        $this->assertEmpty($actual);
    }
}
