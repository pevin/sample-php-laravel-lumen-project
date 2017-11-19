<?php

namespace Tests\Account;

use App\Request\RequestService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use \ReflectionClass;
use Tests\TestCase;

class RequestServiceTest extends TestCase
{
    public function testSend()
    {
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, []),
            new GuzzleResponse(
                Response::HTTP_NOT_FOUND,
                [],
                json_encode([
                    'status_code' => Response::HTTP_NOT_FOUND,
                    'message' => 'this is a message'
                ])
            ),
        ]);
        $request = new Request(
            'GET',
            "/test"
        );

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $class = new ReflectionClass('App\Request\RequestService');
        $sendMethod = $class->getMethod('send');
        $sendMethod->setAccessible(true);

        $requestService = new RequestService($client);

        // test Response::HTTP_OK
        $response = $sendMethod->invokeArgs($requestService, [$request]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // test Response::HTTP_NOT_FOUND
        $this->expectException(HttpException::class);
        $response = $sendMethod->invokeArgs($requestService, [$request]);
    }
}
