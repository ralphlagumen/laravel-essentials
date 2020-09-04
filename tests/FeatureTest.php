<?php

namespace Lagumen\Essential\Tests;

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
     * Get the service providers for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Lagumen\Essential\Tests\TestServiceProvider',
            'Lagumen\Essential\LaravelEssentialServiceProvider',
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
