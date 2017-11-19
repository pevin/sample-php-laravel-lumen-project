<?php

namespace App\Providers;

use App\Department\DepartmentRequestService;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use App\Employee\EmployeeRequestService;
use App\User\UserRequestService;
use App\Company\CompanyRequestService;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * Register any Request Services.
     *
     * @return void
     */
    public function register()
    {
        $companyUri = getenv('COMPANY_API_URI');
        $this->app->bind(EmployeeRequestService::class, function () use ($companyUri) {
            return new EmployeeRequestService(new Client([
                'base_uri' => $companyUri
            ]));
        });
        $this->app->bind(UserRequestService::class, function () use ($companyUri) {
            return new UserRequestService(new Client([
                'base_uri' => $companyUri
            ]));
        });
        $this->app->bind(CompanyRequestService::class, function () use ($companyUri) {
            return new CompanyRequestService(new Client([
                'base_uri' => $companyUri
            ]));
        });
        $this->app->bind(DepartmentRequestService::class, function () use ($companyUri) {
            return new DepartmentRequestService(new Client([
                'base_uri' => $companyUri
            ]));
        });
    }

    public function boot()
    {
    }
}
