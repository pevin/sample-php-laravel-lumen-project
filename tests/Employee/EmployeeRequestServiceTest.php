<?php

namespace Tests\Employee;

use App\Employee\EmployeeRequestService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeRequestServiceTest extends \Tests\TestCase
{
    public function testGetEmployee()
    {
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, []),
            new GuzzleResponse(Response::HTTP_NOT_FOUND, []),
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $requestService = new EmployeeRequestService($client);

        // test Response::HTTP_OK
        $response = $requestService->getEmployee(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // test Response::HTTP_NOT_FOUND
        $this->assertNull($requestService->getEmployee(1));
    }

    public function testGetEmployeeIds()
    {
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, []),
            new GuzzleResponse(Response::HTTP_NOT_FOUND, []),
            new GuzzleResponse(Response::HTTP_NOT_ACCEPTABLE, [])
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $requestService = new EmployeeRequestService($client);

        // test Response::HTTP_OK
        $response = $requestService->getEmployeeIds();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // test Response::HTTP_NOT_FOUND
        $this->expectException(HttpException::class);
        $requestService->getEmployeeIds();

        // test Response::HTTP_NOT_ACCEPTABLE
        $this->expectException(HttpException::class);
        $requestService->getEmployeeIds();
    }

    public function testGetCompanyEmployeesByAttribute()
    {
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, []),
            new GuzzleResponse(Response::HTTP_UNAUTHORIZED, []),
            new GuzzleResponse(Response::HTTP_NOT_ACCEPTABLE, [])
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $requestService = new EmployeeRequestService($client);

        // test Response::HTTP_OK
        $response = $requestService->getCompanyEmployeesByAttribute(1, 'test', []);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // test Response::HTTP_UNAUTHORIZED
        $this->expectException(HttpException::class);
        $requestService->getCompanyEmployeesByAttribute(1, 'test', []);

        // test Response::HTTP_NOT_ACCEPTABLE
        $this->expectException(HttpException::class);
        $requestService->getCompanyEmployeesByAttribute(1, 'test', []);
    }
}
