<?php

namespace Tests\Department;

use App\Department\DepartmentRequestService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DepartmentRequestServiceTest extends \Tests\TestCase
{
    public function testGet()
    {
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, []),
            new GuzzleResponse(Response::HTTP_UNAUTHORIZED, []),
            new GuzzleResponse(Response::HTTP_NOT_FOUND, []),
            new GuzzleResponse(Response::HTTP_NOT_ACCEPTABLE, [])
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $requestService = new DepartmentRequestService($client);

        // test Response::HTTP_OK
        $response = $requestService->get(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());


        // test Response::HTTP_UNAUTHORIZED
        $this->expectException(HttpException::class);
        $requestService->get(1);

        // test Response::HTTP_NOT_FOUND
        $this->expectException(HttpException::class);
        $requestService->get(1);

        // test Response::HTTP_NOT_ACCEPTABLE
        $this->expectException(HttpException::class);
        $requestService->get(1);
    }

    public function testGetIds()
    {
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, []),
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $requestService = new DepartmentRequestService($client);

        // test Response::HTTP_OK
        $response = $requestService->getIds();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
