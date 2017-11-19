<?php

namespace Tests\Employee;

use App\Employee\EmployeeRequestService;
use App\Employee\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Mockery;
use Tests\TestCase;

class EmployeeServiceTest extends TestCase
{
    public function testGetEmployeesByDepartmentIds()
    {
        $expected = [
            [
                'employee_id' => '1234567',
            ],
            [
                'employee_id' => '1234567',
            ],
            [
                'employee_id' => '1234567',
            ],
            [
                'employee_id' => '1234567',
            ],
            [
                'employee_id' => '1234567',
            ],
        ];

        $mockRequestService = Mockery::mock(EmployeeRequestService::class);
        $mockRequestService->shouldReceive('getCompanyEmployeesByAttribute')
            ->andReturn(new JsonResponse(json_encode($expected)));

        $employeeService = new EmployeeService($mockRequestService);
        $actual = $employeeService->getEmployeesByDepartmentId(1, 1);

        $this->assertEquals($expected, $actual);
    }

    public function testGetEmployeesByDepartmentIdsWithErrorOnRequest()
    {
        Log::shouldReceive('error')->once();
        $mockRequestService = Mockery::mock(EmployeeRequestService::class);
        $mockRequestService
            ->shouldReceive('getCompanyEmployeesByAttribute')
            ->once()
            ->andThrow(HttpException::class);
        $service = new EmployeeService($mockRequestService);

        $actual = $service->getEmployeesByDepartmentId(1, 1);

        $this->assertEmpty($actual);
    }
}
