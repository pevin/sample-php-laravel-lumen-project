<?php

namespace App\Department;

use App\ES\ESIndexQueueService;
use App\ES\DocumentType\DepartmentDocument;
use App\ES\ESReindexService;

class DepartmentESReindexService extends ESReindexService
{
    /**
     * @var \App\Department\DepartmentService
     */
    protected $departmentService;

    /**
     * @var \App\ES\ESIndexQueueService
     */
    protected $queueService;

    public function __construct(
        DepartmentService $departmentService,
        ESIndexQueueService $queueService
    ) {
        $this->departmentService = $departmentService;
        $this->queueService = $queueService;
    }

    public function reindex()
    {
        $departmentIds = $this->departmentService->getIds();

        $details = [
            'id' => $departmentIds,
            'type' => DepartmentDocument::TYPE,
        ];

        $this->queueService->queue($details);
    }
}
