<?php

namespace App\Department;

use App\Employee\EmployeeService;
use App\ES\DocumentType\DepartmentDocument;
use App\ES\DocumentType\EmployeeDocument;
use App\ES\ESIndexServiceException;
use App\ES\ESIndexQueueService;
use App\ES\ESIndexProcessable;
use App\Model\Department;

class DepartmentESIndexService implements ESIndexProcessable
{
    const DEPARTMENT_IDS_THRESHOLD = 50;

    /**
     * @var \App\Department\DepartmentService
     */
    private $departmentService;

    /**
     * @var \App\Employee\EmployeeService
     */
    private $employeeService;

    /**
     * @var \App\ES\ESIndexQueueService
     */
    protected $esIndexQueueService;

    public function __construct(
        DepartmentService $departmentService,
        EmployeeService $employeeService,
        ESIndexQueueService $esIndexQueueService
    ) {
        $this->esIndexQueueService = $esIndexQueueService;
        $this->departmentService = $departmentService;
        $this->employeeService = $employeeService;
    }

    public function process(array $task)
    {
        if (empty($task['id'])) {
            throw new ESIndexServiceException('Missing ids on department ES index task');
        }

        $departmentIds = $task['id'];

        if (!is_array($departmentIds)) {
            throw new ESIndexServiceException('Ids on department ES index task should be an array');
        }

        if (count($departmentIds) > self::DEPARTMENT_IDS_THRESHOLD) {
            $this->enqueueIds($departmentIds);
            return;
        }

        foreach ($departmentIds as $departmentId) {
            $this->index($departmentId);
        }
    }

    /**
     * Enqueue Department ids per threshold
     *
     * @param array $departments
     */
    protected function enqueueIds(array $departments)
    {
        $departmentChunks = collect($departments)->chunk(self::DEPARTMENT_IDS_THRESHOLD);
        $departmentChunks->each(function ($departmentIds) {
            $details = [
                'id' => $departmentIds,
                'type' => DepartmentDocument::TYPE,
            ];

            $this->esIndexQueueService->queue($details);
        });
    }

    /**
     * Index department to ES
     *
     * @param int $departmentId
     */
    protected function index(int $departmentId)
    {
        $departmentData = $this->departmentService->get($departmentId);

        if (!empty($departmentData)) {
            $department = $this->getDepartment($departmentData);
            $department->save();

            $employees = $this->employeeService->getEmployeesByDepartmentId(
                $departmentData['company_id'],
                $departmentData['id']
            );
            if (!empty($employees)) {
                $employeeIds = collect($employees)->pluck('id');
                $details = [
                    'id' => $employeeIds,
                    'type' => EmployeeDocument::TYPE,
                ];

                $this->esIndexQueueService->queue($details);
            }
        }
    }

    /**
     * Get new department
     *
     * @param array $departmentDetails
     * @return \App\Model\Department
     */
    protected function getDepartment(array $departmentDetails)
    {
        return new Department([
            'name' => $departmentDetails['name'],
            'parent_name' => $departmentDetails['parent']['name'] ?? '',
            'company_id' => $departmentDetails['company_id'],
            '_id' => $departmentDetails['id'],
        ]);
    }
}
