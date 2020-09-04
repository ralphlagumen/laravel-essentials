<?php

namespace Lagumen\LaravelEssential\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Route::namespace('Lagumen\LaravelEssential\Tests\Controllers')
            ->middleware('web')
            ->group(function () {
                Route::post('/users/create', 'UsersController@store')->name('users.store');
                Route::get('/users', 'UsersController@index')->name('users.index');
                Route::get('/users/{id}', 'UsersController@show')->name('users.show');
            });
    }
}
