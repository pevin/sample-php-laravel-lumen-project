<?php

namespace Tests;

use App\Department\DepartmentESIndexService;
use App\Employee\EmployeeESIndexService;
use App\ES\DocumentType\DepartmentDocument;
use App\ES\ESIndexServiceException;
use App\ES\ESIndexServiceFactory;
use App\ES\DocumentType\EmployeeDocument;

class ESIndexServiceFactoryTest extends TestCase
{
    public function testUnsupportedType()
    {
        $factory = new ESIndexServiceFactory();

        $this->expectException(ESIndexServiceException::class);
        $factory->createService('thisTypeIsNotSupported');
    }

    public function testEmployeeType()
    {
        $factory = new ESIndexServiceFactory();

        $service = $factory->createService(EmployeeDocument::TYPE);

        $this->assertInstanceOf(EmployeeESIndexService::class, $service);
    }

    public function testDepartmentType()
    {
        $factory = new ESIndexServiceFactory();

        $service = $factory->createService(DepartmentDocument::TYPE);

        $this->assertInstanceOf(DepartmentESIndexService::class, $service);
    }
}
