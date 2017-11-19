<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use Helpers;

    // Returns \Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE
    protected function invalidRequestError($msg = 'Your request was invalid.')
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException($msg);
    }

    // Returns \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
    protected function notFoundError($msg = 'Request not found.')
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException($msg);
    }

    /*
    * Overrides method of \Laravel\Lumen\Routing\ProvidesConvenienceMethods
    */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $this->makeValidation($request->all(), $rules, $messages, $customAttributes);
    }

    public function validateArray(array $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $this->makeValidation($request, $rules, $messages, $customAttributes);
    }

    public function makeValidation($request, $rules, $messages, $customAttributes)
    {
        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $error = current($this->formatValidationErrors($validator));
            $this->invalidRequestError(current($error));
        }
    }
}
