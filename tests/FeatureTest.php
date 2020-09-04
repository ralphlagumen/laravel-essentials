<?php

namespace Lagumen\LaravelEssential\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase;

class FeatureTest extends TestCase
{
    /**
     * Setup the test environment
     *
     * @return  void
     */
    protected function setUp() : void
    {
        parent::setUp();

        Config::set('laravel_essential.validation_namespace', 'App\Http\Validations');
        Config::set('laravel_essential.repository_namespace', 'App\Repositories');
        Config::set('laravel_essential.action_namespace', 'App\Actions');
        Config::set('laravel_essential.filter_namespace', 'App\Filters');

        $this->loadMigrations();

        $this->withFactories(__DIR__.'/Factories');

        TestResponse::macro('data', function ($key = null) {
            if (! $key) {
                return $this->original;
            }
            if ($this->original instanceof Collection) {
                return $this->original->{$key};
            }
            return $this->original->getData()['key'];
        });
    }

    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--path'     => realpath(__DIR__.'/Migrations'),
        ]);
    }

    /**
     * Get the service providers for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Lagumen\LaravelEssential\Tests\TestServiceProvider',
            'Lagumen\LaravelEssential\LaravelEssentialServiceProvider',
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
