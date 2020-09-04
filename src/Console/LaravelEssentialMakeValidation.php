<?php

namespace Lagumen\Essential\Console;

use Illuminate\Console\GeneratorCommand;

class LaravelEssentialMakeValidation extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:essential-validation {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new validation class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Validation';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Stubs/Validation.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('laravel_essential.validation_namespace');
    }
}
