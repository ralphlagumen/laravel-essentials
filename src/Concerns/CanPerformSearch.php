<?php

namespace Lagumen\LaravelEssential\Concerns;

use Lagumen\LaravelEssential\LaravelEssentialSearchableModel;

trait CanPerformSearch
{
    public function scopeSearch($query, $value)
    {
        if (empty($this->searchableColumns)) {
            return $this;
        }

        return $query->where(function ($builder) use ($value) {
            foreach ($this->searchableColumns as $key => $column) {
                if (is_array($column)) {
                    $builder->orWhereHas($key, function ($builder) use ($column, $value) {
                        foreach ($column as $relationColumn) {
                            $builder->where($relationColumn, 'LIKE', '%'.$value.'%');
                        }
                    });
                } else {
                    $builder->orWhere($builder->qualifyColumn($column), 'LIKE', '%'.$value.'%');
                }
            }
        });
    }

    public function scopeFilter($query, $value = [])
    {
        return app(LaravelEssentialSearchableModel::class)
            ->builder($query)
            ->filter($value);
    }
}
