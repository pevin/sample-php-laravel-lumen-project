<?php

namespace App\Employee;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmployeeService
{
    /**
     * @var EmployeeRequestService
     */
    protected $employeeRequestService;

    public function __construct(EmployeeRequestService $employeeRequestService)
    {
        $this->employeeRequestService = $employeeRequestService;
    }

    /**
     * Get employees by department id
     * @param int $companyId Company Id
     * @param int $departmentId department Id
     * @return array Employee data
     */
    public function getEmployeesByDepartmentId(int $companyId, int $departmentId)
    {
        try {
            $formData = [
                'values' => $departmentId
            ];
            $employeeData = $this->employeeRequestService->getCompanyEmployeesByAttribute(
                $companyId,
                'department_id',
                $formData
            );

            $employees = json_decode($employeeData->getData(), true);
        } catch (HttpException $e) {
            $employees = [];
            Log::error($e->getMessage());
        }
        return $employees;
    }
}
