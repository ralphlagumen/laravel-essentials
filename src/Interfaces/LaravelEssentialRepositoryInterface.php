<?php

namespace Lagumen\LaravelEssential\Interfaces;

interface LaravelEssentialRepositoryInterface
{
    public function all();

    public function count();

    public function create(array $data);

    public function createMultiple(array $data);

    public function delete();

    public function deleteById($id);

    public function deleteMultipleById(array $ids);

    public function first();

    public function get();

    public function getById($id);

    public function limit($limit);

    public function orderBy($column, $value);

    public function updateById($id, array $data);

    public function where($column, $value, $operator = '=');

    public function whereIn($column, $value);

    public function with($relations);

    public function findByColumn($value);
}
