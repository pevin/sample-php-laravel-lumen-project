<?php

namespace App\Employee;

use App\ES\ESIndexQueueService;
use App\ES\DocumentType\EmployeeDocument;
use App\ES\ESReindexService;

class EmployeeESReindexService extends ESReindexService
{
    /**
     * @var \App\Employee\EmployeeRequestService
     */
    protected $employeeRequestService;

    /**
     * @var \App\ES\ESIndexQueueService
     */
    protected $queueService;

    public function __construct(
        EmployeeRequestService $employeeRequestService,
        ESIndexQueueService $queueService
    ) {
        $this->employeeRequestService = $employeeRequestService;
        $this->queueService = $queueService;
    }

    public function reindex()
    {
        $employeeIds = $this->getExistingEmployeeIds();

        $details = [
            'id' => $employeeIds,
            'type' => EmployeeDocument::TYPE,
        ];

        $this->queueService->queue($details);
    }

    /**
     * Get existing employee ids
     *
     * @return array
     */
    protected function getExistingEmployeeIds()
    {
        $employeeIdsResponse = $this->employeeRequestService->getEmployeeIds();

        return json_decode($employeeIdsResponse->getData(), true);
    }
}
