<?php

namespace Lagumen\LaravelEssential\Tests\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Lagumen\LaravelEssential\Tests\Repositories\UserRepository;
use Lagumen\LaravelEssential\Tests\Validations\UserValidation;

class UsersController extends Controller
{
    protected $repository;

    protected $validations;

    public function __construct(UserValidation $validation, UserRepository $repository)
    {
        $this->validations = $validation;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $users = $this->repository->getAllFilteredUsers($request->all());

        return response()->json($users);
    }

    public function show($id)
    {
        $user = $this->repository->getById($id);

        return response()->json($user);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), $this->validations->save())->validate();

        $user = $this->repository->createUser($request->all());

        return response()->json($user);
    }
}
