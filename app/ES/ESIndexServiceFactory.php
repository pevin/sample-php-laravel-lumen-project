<?php

namespace App\ES;

use App\Department\DepartmentESIndexService;
use App\Employee\EmployeeESIndexService;
use App\ES\DocumentType\DepartmentDocument;
use App\ES\DocumentType\EmployeeDocument;

class ESIndexServiceFactory
{
    const ES_INDEX_SERVICES = [
        EmployeeDocument::TYPE => EmployeeESIndexService::class,
        DepartmentDocument::TYPE => DepartmentESIndexService::class,
    ];

    /**
     * Create ESIndexService
     *
     * @param string $type
     * @return ESIndexProcessable
     * @throws ESIndexServiceException
     */
    public function createService(string $type)
    {
        if (empty(self::ES_INDEX_SERVICES[$type])) {
            throw new ESIndexServiceException('ES index service type not supported');
        }

        return app()->make(self::ES_INDEX_SERVICES[$type]);
    }
}
