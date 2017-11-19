<?php

namespace App\User;

use App\Request\RequestService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class UserRequestService extends RequestService
{

    /**
     * Constructor
     *
     * @param \GuzzleHttp\Client $client Guzzle client
     *
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Call endpoint to fetch user details
     *
     * @param string $id User ID
     * @return \Illuminate\Http\JsonResponse User information
     *
     */
    public function get(string $id)
    {
        $request = new Request(
            'GET',
            "/user/{$id}"
        );
        return $this->send($request);
    }
}
