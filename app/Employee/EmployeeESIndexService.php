<?php

namespace App\Employee;

use App\ES\ESIndexServiceException;
use App\ES\ESIndexQueueService;
use App\ES\ESIndexProcessable;
use App\ES\DocumentType\EmployeeDocument;
use App\Model\Employee;

class EmployeeESIndexService implements ESIndexProcessable
{
    const EMPLOYEE_IDS_THRESHOLD = 50;

    /**
     * @var \App\Employee\EmployeeRequestService
     */
    protected $employeeRequestService;

    /**
     * @var \App\ES\ESIndexQueueService
     */
    protected $esIndexQueueService;

    public function __construct(
        EmployeeRequestService $employeeRequestService,
        ESIndexQueueService $esIndexQueueService
    ) {
        $this->employeeRequestService = $employeeRequestService;
        $this->esIndexQueueService = $esIndexQueueService;
    }

    public function process(array $task)
    {
        if (empty($task['id'])) {
            throw new ESIndexServiceException('Missing ids on employee ES index task');
        }

        $employeeIds = $task['id'];

        if (!is_array($employeeIds)) {
            throw new ESIndexServiceException('Ids on employee ES index task should be an array');
        }

        if (count($employeeIds) > self::EMPLOYEE_IDS_THRESHOLD) {
            $this->enqueueEmployeeIds($employeeIds);
            return;
        }

        foreach ($employeeIds as $employeeId) {
            $this->indexEmployee($employeeId);
        }
    }

    /**
     * Enqueue Employee ids per threshold
     *
     * @param array $employees
     */
    protected function enqueueEmployeeIds(array $employees)
    {
        $employeeChunks = collect($employees)->chunk(self::EMPLOYEE_IDS_THRESHOLD);
        $employeeChunks->each(function ($employeeIds) {
            $details = [
                'id' => $employeeIds,
                'type' => EmployeeDocument::TYPE,
            ];

            $this->esIndexQueueService->queue($details);
        });
    }

    /**
     * Index employee to ES
     *
     * @param int $employeeId
     */
    protected function indexEmployee(int $employeeId)
    {
        $employeeData = $this->employeeRequestService->getEmployee($employeeId);

        if (!empty($employeeData)) {
            $employeeDetails = json_decode($employeeData->getData(), true);
            $employee = $this->getEmployee($employeeDetails);
            $employee->save();
        }
    }

    /**
     * Get new employee
     *
     * @param array $employeeDetails
     * @return \App\Model\Employee
     */
    protected function getEmployee(array $employeeDetails)
    {
        return new Employee([
            'employee_number' => $employeeDetails['employee_id'],
            'first_name' => $employeeDetails['first_name'],
            'middle_name' => $employeeDetails['middle_name'],
            'last_name' => $employeeDetails['last_name'],
            'hours_per_day' => $employeeDetails['hours_per_day'],
            'date_hired' => $employeeDetails['date_hired'],
            'date_ended' => $employeeDetails['date_ended'],
            'location_name' => $employeeDetails['location_name'],
            'department_name' => $employeeDetails['department_name'],
            'company_id' => $employeeDetails['company_id'],
            'active' => $employeeDetails['active'],
            '_id' => $employeeDetails['id'],
        ]);
    }
}
