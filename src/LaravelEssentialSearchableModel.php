<?php

namespace Lagumen\LaravelEssential;

use Illuminate\Support\Str;

class LaravelEssentialSearchableModel
{
    private static $laravelEssentialSearchableModel;

    protected $model;

    protected $filters;

    protected $onlyFirst;

    /**
     * Protected constructor to prevent creating a new instance of the
     * singleton via the `new` operator.
     */
    private function __construct()
    {
        $this->onlyFirst = false;
    }

    public static function getInstance()
    {
        if (null === self::$laravelEssentialSearchableModel) {
            self::$laravelEssentialSearchableModel = new self();
        }

        return self::$laravelEssentialSearchableModel;
    }

    /**
     * This will sort, search depending on the query request given by the front end
     *
     * for example: sort: id|asc OR search: john doe
     *
     * Searching for column will vary depending on the assigned values inside the $searchableColumns which can be set on model
     * See \App\Models\User, a trait called CanPerformSearch is required to be used on a specified model.
     *
     * @param  array  $data
     * @return mixed
     */
    public function build(array $data = [])
    {
        $this->filterQueryBuilder($data);

        if (!empty($data['search'])) {
            $this->model = $this->model->search($data['search']);
        }

        if (!empty($data['sort'])) {
            $sort = explode('|', $data['sort']);
            $this->model = $this->model->orderBy($this->model->qualifyColumn($sort[0]), $sort[1]);
        }

        if ($this->onlyFirst) {
            return $this->model->first();
        }

        return empty($data['per_page']) ? $this->model->get() : $this->model->paginate();
    }

    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    protected function filterQueryBuilder($data)
    {
        $this->findFilters($data);

        if ($this->filters) {
            collect($this->filters)->map(function ($filter) {
                $this->model = (new $filter['filter'])($this->model, $filter['value']);
            });
        }
    }

    /**
     * To check if filter exists, if exists return the filter namespace
     *
     *
     * @param  array  $data
     * @return string|array
     */
    protected function findFilters(array $data = [])
    {
        collect(array_keys($data))->map(function ($key) use (&$filterClass, $data) {
            $model = class_basename($this->model->getModel());
            $className = $this->formatStringToClassStructure($key);

            if (class_exists(config('laravel_essential.filter_namespace') . "\\{$model}\\{$className}")) {
                $this->filters[] = [
                    'filter' => config('laravel_essential.filter_namespace') . "\\{$model}\\{$className}",
                    'value'  => $data[$key]
                ];
            }
        });

    }

    protected function formatStringToClassStructure($string)
    {
        $arrString = explode("_", $string);

        return collect($arrString)->map(function ($value) {
            return Str::ucfirst($value);
        })->implode('', '');
    }

    public function onlyFirst()
    {
        $this->onlyFirst = true;

        return $this;
    }
}
