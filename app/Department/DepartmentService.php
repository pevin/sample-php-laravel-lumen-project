<?php

namespace App\Department;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DepartmentService
{
    /**
     * @var \App\Department\DepartmentRequestService
     */
    private $requestService;

    /**
     * DepartmentService constructor
     * @param \App\Department\DepartmentRequestService $requestService
     */
    public function __construct(DepartmentRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Get department
     * @param  int    $id Department Id
     * @return array
     */
    public function get(int $id)
    {
        try {
            $response = $this->requestService->get($id);
            $department = json_decode($response->getData(), true);
        } catch (HttpException $e) {
            $department = [];
            Log::error($e->getMessage());
        }
        return $department;
    }

    /**
     * Get department ids
     * @return array
     */
    public function getIds()
    {
        try {
            $response = $this->requestService->getIds();
            $ids = json_decode($response->getData(), true);
        } catch (HttpException $e) {
            $ids = [];
            Log::error($e->getMessage());
        }
        return $ids;
    }
}
