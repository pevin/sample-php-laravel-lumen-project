<?php

namespace App\Http\Controllers;

use App\Model\Employee;
use App\View\EmployeeView;
use App\User\UserService;
use App\Company\CompanyService;
use App\Transformer\EmployeeViewTransformer;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection;

class EmployeeController extends Controller
{
    /**
     * @var \App\User\UserService
     */
    private $userService;

    /**
     * @var \App\Company\CompanyService
     */
    private $companyService;

    /**
     * @var \App\Transformer\EmployeeViewTransformer
     */
    private $transformer;

    public function __construct(
        UserService $userService,
        CompanyService $companyService,
        FractalManager $fractal,
        EmployeeViewTransformer $transformer
    ) {
        $this->userService = $userService;
        $this->companyService = $companyService;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    /**
     * @SWG\Post(
     *     path="/employee/",
     *     summary="Create Employee View",
     *     description="Create Employee View",
     *     tags={"employee"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="View Options",
     *         required=true,
     *         schema={
     *             "type"="object",
     *             "properties"={
     *                  "user_id"={"type"="integer"},
     *                  "company_id"={"type"="integer"},
     *                  "name"={"type"="string"},
     *                  "mode"={"type"="string"},
     *                  "columns"={
     *                      "type"="array",
     *                      "items"={
     *                          "type"="string"
     *                      }
     *                  },
     *                  "filters"={
     *                      "type"="array",
     *                      "items"={
     *                          "type"="object",
     *                          "properties"={
     *                              "field"={"type"="string"},
     *                              "condition"={"type"="string"},
     *                              "value"={"type"="string"}
     *                          }
     *                      }
     *                  }
     *             }
     *         }
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_CREATED,
     *         description="Successful Operation",
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE,
     *         description="Invalid Request",
     *     )
     * )
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $this->invalidRequestError('Invalid Data Schema for View');
            return;
        } else if (
            empty($data['user_id']) ||
            empty($data['company_id']) ||
            empty($data['name']) ||
            empty($data['mode']) ||
            empty($data['filters'])
        ) {
            $this->invalidRequestError('Missing user_id / company_id / name / mode / filters');
            return;
        }

        $user = $this->userService->get($data['user_id']);
        if (empty($user)) {
            $this->notFoundError('User not found.');
        }

        $company = $this->companyService->get($data['company_id']);
        if (empty($company)) {
            $this->notFoundError('Company not found.');
        }

        $this->validate(
            $request,
            [
                'name' => [
                    Rule::unique('employee_views', 'name'),
                ],
            ]
        );

        try {
            $view = new EmployeeView($data);
            $view->save();
        } catch (\InvalidArgumentException $e) {
            $this->invalidRequestError($e->getMessage());
        }

        return $this
            ->response
            ->array([
                'data' => $this->transformer->transform($view->fresh()),
            ])
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @SWG\Patch(
     *     path="/employee/{id}",
     *     summary="Update Employee View",
     *     description="Update Employee View",
     *     tags={"employee"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee View ID",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="View Options",
     *         required=true,
     *         schema={
     *             "type"="object",
     *             "properties"={
     *                  "name"={"type"="string"},
     *                  "mode"={"type"="string"},
     *                  "columns"={
     *                      "type"="array",
     *                      "items"={
     *                          "type"="string"
     *                      }
     *                  },
     *                  "filters"={
     *                      "type"="array",
     *                      "items"={
     *                          "type"="object",
     *                          "properties"={
     *                              "field"={"type"="string"},
     *                              "condition"={"type"="string"},
     *                              "value"={"type"="string"}
     *                          }
     *                      }
     *                  }
     *             }
     *         }
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_NOT_ACCEPTABLE,
     *         description="Invalid Request",
     *     )
     * )
     */
    public function update($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $this->invalidRequestError('Invalid Data Schema for View');
            return;
        } else if (isset($data['user_id'])) {
            $this->invalidRequestError("Can't change owner of view");
            return;
        } else if (isset($data['company_id'])) {
            $this->invalidRequestError("Can't change company of view");
            return;
        }

        $this->validate(
            $request,
            [
                'name' => [
                    Rule::unique('employee_views', 'name')->where(function ($query) use ($id) {
                        $query->where('id', '<>', $id);
                    }),
                ],
            ]
        );

        try {
            $view = EmployeeView::find($id);

            if (empty($view)) {
                $this->notFoundError('Employee view not found.');
            }

            $view->fill($data);
            $view->save();
        } catch (\InvalidArgumentException $e) {
            $this->invalidRequestError($e->getMessage());
        }

        return $this
            ->response
            ->array([
                'data' => $this->transformer->transform($view->fresh()),
            ])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/employee/{id}",
     *     summary="Get Employee View Settings",
     *     description="Get Employee View Settings",
     *     tags={"employee"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee View ID",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
     *         description="View not found",
     *     ),
     * )
     */
    public function get($id)
    {
        $view = EmployeeView::find($id);

        if (empty($view)) {
            $this->notFoundError('Employee view not found.');
        }

        return $this
            ->response
            ->array(['data' => $this->transformer->transform($view)])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/employee/{id}/list",
     *     summary="List Employee View Items",
     *     description="List Employee View Items.
Enables setting of sorting and pagination options.
Option to save sorting and pagination options
",
     *     tags={"employee"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee View ID",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         description="View Options",
     *         required=true,
     *         schema={
     *             "type"="object",
     *             "properties"={
     *                  "save"={"type"="boolean"},
     *                  "sort"={
     *                      "type"="array",
     *                      "items"={
     *                          "type"="object",
     *                          "properties"={
     *                              "field"={"type"="string"},
     *                              "order"={"type"="string"}
     *                          }
     *                      }
     *                  },
     *                  "pagination"={
     *                      "type"="object",
     *                      "properties"={
     *                          "page"={"type"="integer"},
     *                          "per_page"={"type"="integer"}
     *                      }
     *                  }
     *             }
     *         }
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
     *         description="View not found",
     *     ),
     * )
     */
    public function getItems($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $this->invalidRequestError('Invalid Data Schema for View');
            return;
        }

        $view = EmployeeView::find($id);

        if (empty($view)) {
            $this->notFoundError('Employee view not found.');
        }

        $view->fill($data);

        if (
            isset($data['save']) &&
            $data['save'] === true
        ) {
            $view->save();
        }

        return $this
            ->response
            ->array($view->run())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/employee/user/{id}",
     *     summary="Get User's Employee Views",
     *     description="Get User's Employee Views",
     *     tags={"employee"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     ),
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
     *         description="View not found",
     *     ),
     * )
     */
    public function userViews($id)
    {
        $result = new Collection(EmployeeView::userViews($id), $this->transformer);
        return $this
            ->response
            ->array($this->fractal->createData($result)->toArray())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/employee/fields",
     *     summary="Get Employee View Fields",
     *     description="Get Employee View Fields",
     *     tags={"filter"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     )
     * )
     */
    public function fields()
    {
        $fields = EmployeeView::getFieldFilterMap();

        return $this
            ->response
            ->array($fields)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/employee/columns",
     *     summary="Get Employee View Columns",
     *     description="Get Employee View Columns",
     *     tags={"filter"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     )
     * )
     */
    public function columns()
    {
        $columns = EmployeeView::getColumns();

        return $this
            ->response
            ->array($columns)
            ->setStatusCode(Response::HTTP_OK);
    }
}
