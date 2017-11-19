<?php

namespace Tests\Department;

use App\Department\DepartmentESIndexService;
use App\Department\DepartmentService;
use App\Employee\EmployeeService;
use App\ES\ESIndexServiceException;
use App\ES\ESIndexQueueService;
use App\Model\Department;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery as m;

class DepartmentESIndexServiceTest extends TestCase
{
    public function testProcessWithoutDepartmentIds()
    {
        $service = new DepartmentESIndexService(
            m::mock(DepartmentService::class),
            m::mock(EmployeeService::class),
            m::mock(ESIndexQueueService::class)
        );
        $this->expectException(ESIndexServiceException::class);
        $service->process([]);
    }

    public function testProcessWithDepartmentIdsNotArray()
    {
        $service = new DepartmentESIndexService(
            m::mock(DepartmentService::class),
            m::mock(EmployeeService::class),
            m::mock(ESIndexQueueService::class)
        );
        $this->expectException(ESIndexServiceException::class);
        $service->process(['id' => 'NotDepartmentIds']);
    }

    public function testProcessWithMoreThanThreshold()
    {
        $service = new DepartmentESIndexService(
            m::mock(DepartmentService::class),
            m::mock(EmployeeService::class),
            $queueService = m::mock(ESIndexQueueService::class)
        );
        $queueService->shouldReceive('queue')->twice()->andReturnNull();
        $task = [
            'id' => [
                1,2,3,4,5,6,7,8,9,10,
                11,12,13,14,15,16,17,18,19,20,
                21,22,23,24,25,26,27,28,29,30,
                31,32,33,34,35,36,37,38,39,40,
                41,42,43,44,45,46,47,48,49,50,
                51
            ]
        ];
        $service->process($task);
    }

    public function testProcessWithLessThanThreshold()
    {
        $service = m::mock(DepartmentESIndexService::class.'[getDepartment]', [
            $departmentService = m::mock(DepartmentService::class),
            $employeeService = m::mock(EmployeeService::class),
            $queueService = m::mock(ESIndexQueueService::class)
        ]);
        $service->shouldAllowMockingProtectedMethods();
        $department = m::mock(Department::class);
        $department->shouldReceive('save')->once();
        $service->shouldReceive('getDepartment')->once()->andReturn($department);
        $departmentService
            ->shouldReceive('get')
            ->once()
            ->andReturn([
                'id' => 1,
                'company_id' => 1,
                'name' => 'department name',
            ]);
        $employeeService
            ->shouldReceive('getEmployeesByDepartmentId')
            ->once()
            ->andReturn([
                [
                    'id' => 1,
                    'employee' => '10001',
                ]
            ]);
        $queueService->shouldReceive('queue')->once();
        $task = [
            'id' => [1]
        ];
        $service->process($task);
    }

    public function testGetDepartment()
    {
        $service = m::mock(DepartmentESIndexService::class, [
            m::mock(DepartmentService::class),
            $employeeService = m::mock(EmployeeService::class),
            m::mock(ESIndexQueueService::class),
        ]);

        $employeeDetails = [
            'id' => 1,
            'name' => 'some department',
            'parent' => ['name' => 'some parent'],
            'company_id' => 1,
        ];

        $reflectionClass = new \ReflectionClass($service);
        $reflectionMethod = $reflectionClass->getMethod('getDepartment');
        $reflectionMethod->setAccessible(true);
        $actualDepartment = $reflectionMethod->invoke($service, $employeeDetails);

        $this->assertEquals($employeeDetails['id'], $actualDepartment->_id);
        $this->assertEquals($employeeDetails['name'], $actualDepartment->name);
        $this->assertEquals($employeeDetails['company_id'], $actualDepartment->company_id);
    }
}
