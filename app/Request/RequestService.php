<?php

namespace App\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\BadResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestService
{
    /**
     * GuzzleHttp\Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function send(Request $request)
    {
        try {
            $response = $this->client->send($request);
            return response()->json($response->getBody()->getContents(), $response->getStatusCode());
        } catch (BadResponseException $e) {
            // we catch 400 and 500 errors from the micro-service, then return as HttpExceptions
            $contents = json_decode($e->getResponse()->getBody()->getContents());
            if ($contents) {
                throw new HttpException($contents->status_code, $contents->message, $e);
            }
            $response = $e->getResponse();
            throw new HttpException($response->getStatusCode(), $response->getReasonPhrase(), $e);
        }
    }
}
