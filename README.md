# Laravel Essentials
This package provides all necessary utilities needed to build a small to large scale projects.

Inspired by [Laravel Query Filters](https://github.com/ambengers/laravel-query-filter)

[![Build Status](https://travis-ci.com/ralphlagumen/laravel-essentials.svg?branch=master)](https://travis-ci.com/ralphlagumen/laravel-essentials)
[![StyleCI](https://github.styleci.io/repos/292729972/shield?branch=master)](https://github.styleci.io/repos/292729972?branch=master)

# Installation
Run the following command inside your project.
```
composer require lagumen/laravel-essentials
```

Optionally you can publish the config file by running the following command.
```
php artisan vendor:publish --tag=laravel-essential-config
```
The config file contains the default namespace for `Actions`, `Validations`, `Repositories` and `Filters`. You can change the default namespace depending on your preference.

# Usage
## Repositories
You can create your Repository Class by running the following command.

```
php artisan make:essential-repository UserRepository
```

This will create an Repository Class on `App\Repositories` by default.
```
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
    
    // We will use this later. ;)
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
```
To apply this, just go ahead and initialize your Repository Class to your Controller.

```
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lagumen\LaravelEssential\Tests\Repositories\UserRepository;

class UsersController extends Controller
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function index(Request $request)
    {
        $users = $this->repository->getAllFilteredUsers($request->all());

        return response()->json($users);
    }
    
    public function store(Request $request)
    {
        $user = $this->repository->createUser($request->all());

        return response()->json($user);
    }
}
```


## Validations
Of course, you need to validate your request before inserting or updating a data in your database.

You can make your validation class by running the following command.

```
php artisan make:essential-validation UserValidation
```

This will create a Validation Class on `App\Http\Validations` by default.

```
use Lagumen\LaravelEssential\Interfaces\LaravelEssentialValidationInterface;

class UserValidation implements LaravelEssentialValidationInterface
{
    public function save(array $data = [])
    {
        return [
            'name'  => 'required|string',
            'email' => 'required|unique:users,email',
        ];
    }

    public function update(array $data = [])
    {
        return [
            'name'  => 'sometimes|string',
            'email' => 'sometimes|unique:users,email,'.$data['id'], // ignore self
        ];
    }
}
```

To apply this, just go ahead and call the `Validator` Facade inside your controller.

```
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Lagumen\LaravelEssential\Tests\Validations\UserValidation;

class UsersController extends Controller
{
  protected $validations;

  public function __construct(UserValidation $validation)
  {
      $this->validations = $validation;
  }
  
  public function store(Request $request)
  {
      Validator::make($request->all(), $this->validations->save())->validate();

      $user = $this->repository->createUser($request->all());

      return response()->json($user);
  }
  
  public function update(Request $request, $id)
  {
      Validator::make($request->all(), $this->validations->update())->validate();

      $user = $this->repository->createUser($request->all());

      return response()->json($model);
  }
}

```
No, need to create multiple Request Class for a single controller. 

## Actions
You can create your Action Class by running the following command.

```
php artisan make:essential-action UserTypeAction
```

This will create an Action Class on `App\Actions` by default.

```
use Lagumen\Essential\Interfaces\LaravelEssentialActionInterface;
use App\Models\User;

class UserTypeAction implements LaravelEssentialActionInterface
{
    protected $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Execute action
     *
     * @param  array  $data
     * @return mixed
     */
    public function execute(array $data = [])
    {
        if ($this->user->isAdmin()) {
           // do logic here...
        }
        
        if ($this->user->isEmployee()) {
           // do logic here...
        }
    }
}
```
This will help you make your controller more managable and cleaner.

```
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Actions\UserTypeAction;

class UsersController extends Controller
{
  public function store(Request $request)
  {
      // Instead of doing this..
      if ($user->isAdmin()) {
         // do logic here...
      }

      if ($user->isEmployee()) {
         // do logic here...
      }
      
      // Try doing this..
      app(UserTypeAction::class ['user' => $user])->execute();
      

      return response()->json($user);
  }
}
```
# Filters

To allow your app to filter, search or sort data, just follow this guides..

First, let's create our Filter. You can do that by running the following command.

```
php artisan make:essential-filter User/Active
```
Notice that I used forward slash, this will create a class on `App\Filters\User\Active.php`.

```
use Illuminate\Database\Eloquent\Builder;

class Active
{
    /**
     * Handle filtering.
     *
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @param string|null                          $value
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $builder, $value)
    {
        return !$value
            ? $builder
            : $builder->where('active', $value);
    }
}
```

Next, let's apply the `CanPerformSearch` trait on the model that you want to allow searching and set the columns that you want to allow to be searched, by calling `$searchableColumns`.

```
use Illuminate\Database\Eloquent\Model;
use Lagumen\LaravelEssential\Concerns\CanPerformSearch;

class User extends Model
{
    use CanPerformSearch;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    protected $searchableColumns = [
        'name',
        'setting' => ['timezone'],
    ];

    public function setting()
    {
        return $this->hasOne(UserSetting::class, 'user_id');
    }
}

```
You also need to call `LaravelEssentialSearchableModel` on either your Controller or Repository. You can do that by doing this.

```
return LaravelEssentialSearchableModel::getInstance()
          ->builder($this->model->query())
          ->filter($filters);
```

Or, you can check what I did on the [Repositories](https://github.com/ralphlagumen/laravel-essentials/blob/master/README.md#repositories) above. ;)

Now, you can perform filtering, searching and sorting by passing a request parameters to url:
```
/users?sort=id|desc&search=John&active=1
```

