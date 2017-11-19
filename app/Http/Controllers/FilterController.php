<?php

namespace App\Http\Controllers;

use App\Filter\FilterFactory;
use App\Filter\KeywordFilter;
use App\Filter\DateFilter;
use App\Filter\NumberFilter;
use App\Filter\BooleanFilter;
use App\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/filter/types",
     *     summary="Filter Types",
     *     description="Filter types and allowed conditions per type",
     *     tags={"filter"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     )
     * )
     */
    public function types(Request $request)
    {
        $data[FilterFactory::TYPE_KEYWORD] = KeywordFilter::getOptions();
        $data[FilterFactory::TYPE_DATE] = DateFilter::getOptions();
        $data[FilterFactory::TYPE_NUMBER] = NumberFilter::getOptions();
        $data[FilterFactory::TYPE_BOOLEAN] = BooleanFilter::getOptions();

        return $this
            ->response
            ->array($data)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/filter/modes",
     *     summary="Filter Modes",
     *     description="List of allowed filter modes",
     *     tags={"filter"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Successful Operation",
     *     )
     * )
     */
    public function modes(Request $request)
    {
        return $this
            ->response
            ->array(View::getModes())
            ->setStatusCode(Response::HTTP_OK);
    }
}
