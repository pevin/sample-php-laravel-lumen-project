<?php

namespace App\Department;

use App\Request\RequestService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class DepartmentRequestService extends RequestService
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
     * Call endpoint to fetch department details
     *
     * @param string $id Department ID
     * @return \Illuminate\Http\JsonResponse Department information
     *
     */
    public function get(string $id)
    {
        $request = new Request(
            'GET',
            "/department/{$id}"
        );
        return $this->send($request);
    }

    /**
     * Get all internal department ids
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIds()
    {
        $request = new Request(
            'GET',
            "/department/internal_ids"
        );
        return $this->send($request);
    }
}
