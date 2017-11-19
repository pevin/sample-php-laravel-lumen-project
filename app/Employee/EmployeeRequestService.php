<?php

namespace App\Employee;

use App\Request\RequestService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeRequestService extends RequestService
{
    /**
     * Constructor
     *
     * @param \GuzzleHttp\Client $client Guzzle client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Fetch employee for given internal employee id
     *
     * @param int $id Employee ID
     * @return \Illuminate\Http\JsonResponse Employee information
     */
    public function getEmployee(int $id)
    {
        try {
            $request = new Request(
                'GET',
                "/philippine/employee/{$id}"
            );

            return $this->send($request);
        } catch (HttpException $e) {
            return null;
        }
    }

    /**
     * Get all internal employee ids
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployeeIds()
    {
        $request = new Request(
            'GET',
            "/employee/internal_ids"
        );
        return $this->send($request);
    }

    /**
     * Fetch company employees filtering by selected attribute
     *
     * @param int $companyId
     * @param string $attribute
     * @param array $attributeValues
     * @return \Illuminate\Http\JsonResponse List of employees
     */
    public function getCompanyEmployeesByAttribute(int $companyId, string $attribute, array $attributeValues)
    {
        $request = new Request(
            'POST',
            "/company/{$companyId}/employees/{$attribute}",
            [
                'Content-Type' => 'application/x-www-form-urlencoded'

            ],
            http_build_query($attributeValues)
        );
        return $this->send($request);
    }
}
