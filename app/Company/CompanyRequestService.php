<?php

namespace App\Company;

use App\Request\RequestService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class CompanyRequestService extends RequestService
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
     * Call endpoint to fetch Company details
     *
     * @param int $id Company ID
     * @return \Illuminate\Http\JsonResponse Company information
     *
     */
    public function get(int $id)
    {
        $request = new Request(
            'GET',
            "/company/{$id}"
        );
        return $this->send($request);
    }
}
