<?php

namespace Lagumen\LaravelEssential\Tests\Validations;

use Lagumen\LaravelEssential\Interfaces\LaravelEssentialValidationInterface;

class UserValidation implements LaravelEssentialValidationInterface
{
    public function save(array $data = [])
    {
        return [
            'name'  => 'required|string',
            'email' => 'required|unique:users,email'
        ];
    }

    public function update(array $data = [])
    {
        return [
            'name'  => 'sometimes|string',
            'email' => 'sometimes|unique:users,email,' . $data['id'] // ignore self
        ];
    }
}
