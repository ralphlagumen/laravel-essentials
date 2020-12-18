<?php

namespace Lagumen\LaravelEssential;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LaravelEssentialSearchableModel
{
    private static $laravelEssentialSearchableModel;

    protected $builder;

    protected $filters;

    /**
     * This will sort, search depending on the query request given by the front end.
     *
     * for example: sort: id|asc OR search: john doe
     *
     * Searching for column will vary depending on the assigned values inside the $searchableColumns which can be set on model
     * See \App\Models\User, a trait called CanPerformSearch is required to be used on a specified model.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function filter(array $data = [])
    {
        $this->filterQueryBuilder($data);

        if (!empty($data['search'])) {
            $this->builder = $this->builder->search($data['search']);
        }

        if (!empty($data['sort'])) {
            $sort = explode('|', $data['sort']);
            $this->builder = $this->builder->orderBy($this->builder->qualifyColumn($sort[0]), $sort[1]);
        }

        return empty($data['per_page']) ? $this->builder->get() : $this->builder->paginate();
    }

    public function builder(Builder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    protected function filterQueryBuilder($data)
    {
        $this->findFilters($data);

        if ($this->filters) {
            collect($this->filters)->map(function ($filter) {
                $this->builder = (new $filter['filter']())($this->builder, $filter['value']);
            });
        }
    }

    /**
     * To check if filter exists, if exists return the filter namespace.
     *
     *
     * @param array $data
     *
     * @return string|array
     */
    protected function findFilters(array $data = [])
    {
        collect(array_keys($data))->map(function ($key) use (&$filterClass, $data) {
            $model = class_basename($this->builder->getModel());
            $className = $this->formatStringToClassStructure($key);

            if (class_exists(config('laravel_essential.filter_namespace')."\\{$model}\\{$className}")) {
                $this->filters[] = [
                    'filter' => config('laravel_essential.filter_namespace')."\\{$model}\\{$className}",
                    'value'  => $data[$key],
                ];
            }
        });
    }

    protected function formatStringToClassStructure($string)
    {
        $arrString = explode('_', $string);

        return collect($arrString)->map(function ($value) {
            return Str::ucfirst($value);
        })->implode('', '');
    }

    public function destroy()
    {
        self::$laravelEssentialSearchableModel = null;
    }
}
