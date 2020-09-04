<?php

namespace Lagumen\LaravelEssential\Tests\Repositories;

use Lagumen\LaravelEssential\Concerns\LaravelEssentialRepository;
use Lagumen\LaravelEssential\Interfaces\LaravelEssentialRepositoryInterface;
use Lagumen\LaravelEssential\LaravelEssentialSearchableModel;
use Lagumen\LaravelEssential\Tests\Models\User;

class UserRepository extends LaravelEssentialRepository implements LaravelEssentialRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAllFilteredUsers(array $filters = [])
    {
        return LaravelEssentialSearchableModel::getInstance()
            ->builder($this->model->query())
            ->filter($filters);
    }

    public function createUser(array $data)
    {
        /** @var User $user */
        $user = $this->create($data);

        $user->setting()->create(['timezone' => $data['timezone'] ?? 'UTC']);

        return $user->load('setting');
    }
}
