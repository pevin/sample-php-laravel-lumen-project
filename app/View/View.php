<?php

namespace App\View;

use App\Filter\Filter;
use App\Filter\FilterFactory;
use Illuminate\Database\Eloquent\Model;

abstract class View extends Model
{
    /**
     * Array of Filter objects
     */
    protected $filterItems = [];

    const MODE_AND = 'AND';
    const MODE_OR = 'OR';

    const MODES = [
        self::MODE_OR => 'Show results that matches ANY filter',
        self::MODE_AND => 'Show results that matches ALL filters',
    ];

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    protected $allowedSort = [
        self::SORT_ASC,
        self::SORT_DESC,
    ];

    public $fillable = [
        'user_id',
        'company_id',
        'name',
        'mode',
        'columns',
        'filters',
        'sort',
        'pagination',
    ];

    public $casts = [
        'columns' => 'json',
        'filters' => 'json',
        'sort' => 'json',
        'pagination' => 'json',
    ];

    protected $requiredColumns = [];

    protected $optionalColumns = [];

    protected $defaultSort = [];


    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 10;

    protected $defaultPagination= [
        'page' => self::DEFAULT_PAGE,
        'per_page' => self::DEFAULT_PER_PAGE
    ];

    protected $model;

    /**
     * Create a new model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->model = $this->getModel();
    }

    abstract protected function getModel();

    /**
     *
     * Get Filter for given field
     *
     * @return string $field
     *
     */
    public function getFieldFilter($field)
    {
        if (empty($this->fieldMap[$field])) {
            throw new \InvalidArgumentException("Unknown Field type : $field.");
        }

        return FilterFactory::getFilterClass($this->fieldMap[$field]);
    }

    /**
     *
     * Return View mode
     *
     * @return string $mode
     *
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     *
     * Run conditions by AND or OR
     *
     * @param string $mode
     *
     */
    public function setModeAttribute(string $mode)
    {
        if (!in_array($mode, array_keys(self::MODES))) {
            throw new \InvalidArgumentException('Invalid mode.');
        }
        $this->attributes['mode'] = $mode;
    }

    /**
     *
     * Return View Company ID
     *
     * @return int company_id
     *
     */
    public function getCompanyId()
    {
        return $this->company_id;
    }

    /**
     *
     * Check if company id is valid before setting
     *
     * @param $companyId
     *
     */
    public function setCompanyIdAttribute($companyId)
    {
        if (!is_integer($companyId)) {
            throw new \InvalidArgumentException('Company id must be an integer.');
        }
        $this->attributes['company_id'] = $companyId;
    }

    /**
     *
     * Load Filter items after getting self.filters
     *
     * @param string $filters
     *
     */
    public function getFiltersAttribute(string $jsonFilters)
    {
        $filters = json_decode($jsonFilters, true);
        $this->buildFilterItems($filters);
        return $filters;
    }

    /**
     *
     * Load Filter items after setting self.filters
     *
     * @param array $filters
     *
     */
    public function setFiltersAttribute(array $filters)
    {
        $this->buildFilterItems($filters);
        $this->attributes['filters'] = json_encode($filters);
    }

    /**
     *
     * Load Column items after getting self.columns
     *
     * @param $jsonColumns
     *
     */
    public function getColumnsAttribute(string $jsonColumns = null)
    {
        $columns = json_decode($jsonColumns, true);
        if (empty($columns)) {
            $columns = array_merge(
                array_keys($this->requiredColumns),
                array_keys($this->optionalColumns)
            );
        }
        return $columns;
    }

    /**
     *
     * Load Column items after setting self.columns
     *
     * @param array $columns
     *
     */
    public function setColumnsAttribute(array $columns)
    {
        if (empty($columns)) {
            $columns = array_merge(
                array_keys($this->requiredColumns),
                array_keys($this->optionalColumns)
            );
        } else {
            $this->validateColumns($columns);
        }
        $this->attributes['columns'] = json_encode($columns);
    }

    /**
     *
     * Validate columns
     *
     * @param array $columns
     *
     */
    protected function validateColumns(array $columns)
    {
        $requiredFieldsPresent = array_intersect(
            array_keys($this->requiredColumns),
            $columns
        );

        $knownColumns = array_merge(
            array_keys($this->requiredColumns),
            array_keys($this->optionalColumns)
        );

        $validColumns = array_intersect(
            $columns,
            $knownColumns
        );

        if (
            count($requiredFieldsPresent) !=
            count($this->requiredColumns)
        ) {
            throw new \InvalidArgumentException('Not all required columns are present');
        } else if (
            count($columns) !=
            count($validColumns)
        ) {
            throw new \InvalidArgumentException('Not all columns are valid');
        }
    }

    /**
     *
     * Load Sort items after getting self.sort
     *
     * @param $jsonSort
     *
     */
    public function getSortAttribute(string $jsonSort = null)
    {
        $sort = json_decode($jsonSort, true);
        if (empty($sort)) {
            $sort = $this->defaultSort;
        }
        return $sort;
    }

    /**
     *
     * Load Sort items after setting self.sort
     *
     * @param array $sort
     *
     */
    public function setSortAttribute(array $sort)
    {
        if (empty($sort)) {
            $sort = $this->defaultSort;
        } else {
            $this->validateSort($sort);
        }
        $this->attributes['sort'] = json_encode($sort);
    }

    /**
     *
     * Validate sort
     *
     * @param array $sort
     *
     */
    protected function validateSort(array $sort)
    {
        foreach ($sort as $sortItem) {
            if (
                empty($sortItem['field']) ||
                empty($sortItem['order'])
            ) {
                throw new \InvalidArgumentException('Sort fields need field and order attributes');
            } else if (!in_array($sortItem['field'], $this->columns)) {
                throw new \InvalidArgumentException('Sort field not valid: ' . $sortItem['field']);
            } else if (!in_array(strtolower($sortItem['order']), $this->allowedSort)) {
                throw new \InvalidArgumentException('Sort order should be either asc or desc');
            }
        }
    }

    /**
     *
     * Load Pagination items after getting self.pagination
     *
     * @param $jsonPagination
     *
     */
    public function getPaginationAttribute(string $jsonPagination = null)
    {
        $pagination = json_decode($jsonPagination, true);
        if (empty($pagination)) {
            $pagination = $this->defaultPagination;
        }
        return $pagination;
    }

    /**
     *
     * Load Pagination items after setting self.pagination
     *
     * @param array $pagination
     *
     */
    public function setPaginationAttribute(array $pagination)
    {
        if (empty($pagination)) {
            $pagination = $this->defaultPagination;
        } else {
            $this->validatePagination($pagination);
        }
        $this->attributes['pagination'] = json_encode($pagination);
    }

    /**
     *
     * Validate pagination
     *
     * @param array $pagination
     *
     */
    protected function validatePagination(array $pagination)
    {
        if (
            !isset($pagination['page']) ||
            !isset($pagination['per_page'])
        ) {
            throw new \InvalidArgumentException('Pagination needs page and per_page attributes');
        } else if (
            !is_int($pagination['page']) ||
            !is_int($pagination['per_page'])
        ) {
            throw new \InvalidArgumentException('Pagination values need to be integers');
        } else if (
            $pagination['page'] <=0 ||
            $pagination['per_page'] <=0
        ) {
            throw new \InvalidArgumentException('Pagination values need to be greater than zero');
        }
    }

    /**
     *
     * Build Filter items from raw $filters
     *
     * @param array $filters
     *
     */
    protected function buildFilterItems(array $filters)
    {
        if (empty($filters)) {
            throw new \InvalidArgumentException('At least one filter is required.');
        }
        foreach ($filters as $filter) {
            $filterClass = $this->getFieldFilter($filter['field']);
            $value = $filter['value'] ?? null;
            $filterItem = new $filterClass(
                $filter['field'],
                $filter['condition'],
                $value
            );
            $this->addFilterItem($filterItem);
        }
    }

    /**
     *
     * Load Filter items after setting self.filters
     *
     * @param array $filters
     *
     */
    public static function getFieldFilterMap()
    {
        $instance = new static;

        $fieldMapCollection = collect($instance->fieldMap);
        $fieldMapCollection = $fieldMapCollection->map(function ($item, $key) use ($instance) {
            return [
                'label' => $instance->requiredColumns[$key] ?? $instance->optionalColumns[$key],
                'value' => $key,
                'type' => $item,
            ];
        });

        return $fieldMapCollection->values()->all();
    }

    /**
     * Get all available and required columns
     * @return array
     */
    public static function getColumns()
    {
        $instance = new static;

        $columns = collect(
            array_merge(
                $instance->requiredColumns,
                $instance->optionalColumns
            )
        );

        $columns = $columns->map(function ($item, $key) use ($instance) {
            return [
                'label' => $instance->requiredColumns[$key] ?? $instance->optionalColumns[$key],
                'value' => $key,
                'required' => in_array($key, array_keys($instance->requiredColumns)),
            ];
        });

        return $columns->values()->all();
    }

    /**
     * Get all available filter modes
     * @return array
     */
    public static function getModes()
    {
        $modesCollection = collect(self::MODES);
        $modesCollection = $modesCollection->map(function ($item, $key) {
            return [
                'label' => $item,
                'value' => $key,
            ];
        });

        return $modesCollection->values()->all();
    }

    /**
     *
     * Add a filter item to the view
     *
     * @param Filter $filterItem
     *
     */
    protected function addFilterItem(Filter $filterItem)
    {
        $this->filterItems[] = $filterItem;
    }

    /**
     *
     * Run query and receive items for view
     *
     */
    public function run()
    {
        $results = $this->model
            ->newQuery()
            ->body($this->buildSearchBody())
            ->paginate(
                $this->pagination['per_page'],
                "page",
                $this->pagination['page']
            )
            ->toArray();

        // remove pagination links
        unset(
            $results["prev_page_url"],
            $results["next_page_url"],
            $results["path"]
        );

        return $results;
    }

    /**
     *
     * Build search body to use for searching
     *
     * @param Filter $filter
     * @return array $body Search body
     *
     */
    public function buildSearchBody()
    {
        // specify which columns to retrieve
        $body["_source"] = $this->columns;

        $body["query"]["bool"]["must"][] = $this->buildCompanyIdFilter();

        if ($this->mode === self::MODE_AND) {
            $body["query"]["bool"]["must"][] = $this->buildFilterBodies();
        } else {
            $shouldFilters = [];
            $shouldFilters["bool"]["should"] = $this->buildFilterBodies();
            $body["query"]["bool"]["must"][] = $shouldFilters;
        }

        // specify which columns to retrieve
        $body["sort"] = $this->buildSort();

        return $body;
    }

    /**
     *
     *  Construct search parameters for company ID
     *
     */
    protected function buildCompanyIdFilter()
    {
        return [
            "bool" => [
                "must" => [
                    [
                        "term" => [
                            'company_id' => $this->company_id
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     *
     * Loop through view filters to create search body
     *
     * @return array $body Search body
     *
     */
    protected function buildFilterBodies()
    {
        $filterBodies = [];

        foreach ($this->filterItems as $filterItem) {
            $filterBodies[] = $filterItem->buildSearchParameters();
        }

        return $filterBodies;
    }

    /**
     *
     *  Construct sort parameters
     *
     */
    protected function buildSort()
    {
        $sortArray = [];
        foreach ($this->sort as $sortItem) {
            $sortArray[$sortItem['field']] = $sortItem['order'];
        }
        return $sortArray;
    }

    /**
     *
     * Fetches list of user views for this view
     *
     * @param int $userId
     *
     */
    public static function userViews(int $userId)
    {
        return self::where('user_id', $userId)->get();
    }
}
