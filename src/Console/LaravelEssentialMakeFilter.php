<?php

namespace Lagumen\LaravelEssential\Console;

use Illuminate\Console\GeneratorCommand;

class LaravelEssentialMakeFilter extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:essential-filter {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new filter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Stubs/LaravelEssentialFilter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('laravel_essential.filter_namespace');
    }
}
