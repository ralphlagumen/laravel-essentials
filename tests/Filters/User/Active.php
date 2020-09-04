<?php

namespace Lagumen\LaravelEssential\Tests\Filters\User;

use Illuminate\Database\Eloquent\Builder;

class Active
{
    /**
     * Handle filtering
     *
     * @param  Illuminate\Database\Eloquent\Builder  $builder
     * @param  string|null  $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $builder, $value)
    {
        return !$value
            ? $builder
            : $builder->where('active', $value);
    }
}
