<?php

namespace Tests\Employee;

use App\Employee\EmployeeESIndexService;
use App\Employee\EmployeeRequestService;
use App\ES\ESIndexServiceException;
use App\ES\ESIndexQueueService;
use App\Model\Employee;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery as m;

class EmployeeESIndexServiceTest extends TestCase
{
    public function testProcessWithoutEmployeeIds()
    {
        $service = new EmployeeESIndexService(
            m::mock(EmployeeRequestService::class),
            m::mock(ESIndexQueueService::class)
        );
        $this->expectException(ESIndexServiceException::class);
        $service->process([]);
    }

    public function testProcessWithEmployeeIdsNotArray()
    {
        $service = new EmployeeESIndexService(
            m::mock(EmployeeRequestService::class),
            m::mock(ESIndexQueueService::class)
        );
        $this->expectException(ESIndexServiceException::class);
        $service->process(['id' => 'NotEmployeeIds']);
    }

    public function testProcessWithMoreThanThreshold()
    {
        $service = new EmployeeESIndexService(
            m::mock(EmployeeRequestService::class),
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
        $service = m::mock(EmployeeESIndexService::class.'[getEmployee]', [
            $requestService = m::mock(EmployeeRequestService::class),
            m::mock(ESIndexQueueService::class),
        ]);
        $service->shouldAllowMockingProtectedMethods();
        $employee = m::mock(Employee::class);
        $employee->shouldReceive('save')->once();
        $service->shouldReceive('getEmployee')->once()->andReturn($employee);
        $requestService
            ->shouldReceive('getEmployee')
            ->once()
            ->andReturn(
                new JsonResponse(json_encode([
                    'id' => 1,
                    'employee_id' => 'abc10001',
                    'first_name' => 'first name',
                    'middle_name' => 'middle name',
                    'last_name' => 'last name',
                    'hours_per_day' => 8,
                    'date_hired' => '2017-01-01',
                    'date_ended' => '2017-12-01',
                    'department_name' => 'some department',
                    'company_id' => 1,
                    'active' => true,
                ]))
            );
        $task = [
            'id' => [1]
        ];
        $service->process($task);
    }

    public function testGetEmployee()
    {
        $service = m::mock(EmployeeESIndexService::class, [
            m::mock(EmployeeRequestService::class),
            m::mock(ESIndexQueueService::class),
        ]);

        $employeeDetails = [
            'id' => 1,
            'employee_id' => 'abc10001',
            'first_name' => 'first name',
            'middle_name' => 'middle name',
            'last_name' => 'last name',
            'hours_per_day' => 8,
            'date_hired' => '2017-01-01',
            'date_ended' => '2017-12-01',
            'location_name' => 'some location',
            'department_name' => 'some department',
            'company_id' => 1,
            'active' => true,
        ];

        $reflectionClass = new \ReflectionClass($service);
        $reflectionMethod = $reflectionClass->getMethod('getEmployee');
        $reflectionMethod->setAccessible(true);
        $actualEmployee = $reflectionMethod->invoke($service, $employeeDetails);

        $this->assertEquals($employeeDetails['id'], $actualEmployee->_id);
        $this->assertEquals($employeeDetails['employee_id'], $actualEmployee->employee_number);
        $this->assertEquals($employeeDetails['first_name'], $actualEmployee->first_name);
        $this->assertEquals($employeeDetails['middle_name'], $actualEmployee->middle_name);
        $this->assertEquals($employeeDetails['last_name'], $actualEmployee->last_name);
        $this->assertEquals($employeeDetails['hours_per_day'], $actualEmployee->hours_per_day);
        $this->assertEquals($employeeDetails['date_hired'], $actualEmployee->date_hired);
        $this->assertEquals($employeeDetails['date_ended'], $actualEmployee->date_ended);
        $this->assertEquals($employeeDetails['department_name'], $actualEmployee->department_name);
        $this->assertEquals($employeeDetails['company_id'], $actualEmployee->company_id);
        $this->assertEquals($employeeDetails['active'], $actualEmployee->active);
    }
}
