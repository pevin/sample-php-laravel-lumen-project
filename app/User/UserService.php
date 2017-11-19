<?php

namespace App\User;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService
{
    /**
     * @var \App\User\UserRequestService
     */
    private $requestService;

    /**
     * UserService constructor
     * @param \App\User\UserRequestService $requestService
     */
    public function __construct(UserRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Get user
     * @param  int    $id User Id
     * @return array
     */
    public function get(int $id)
    {
        try {
            $response = $this->requestService->get($id);
            $emails = json_decode($response->getData(), true);
        } catch (HttpException $e) {
            $emails = [];
            Log::error($e->getMessage());
        }
        return $emails;
    }
}
