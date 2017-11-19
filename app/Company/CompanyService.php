<?php

namespace App\Company;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CompanyService
{
    private $requestService;

    public function __construct(CompanyRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function get(int $id)
    {
        try {
            $companyData = $this->requestService->get($id);
            $company = json_decode($companyData->getData(), true);
        } catch (HttpException $e) {
            $company = [];
            Log::error($e->getMessage());
        }
        return $company;
    }
}
