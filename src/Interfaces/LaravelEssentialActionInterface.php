<?php


namespace Lagumen\LaravelEssential\Interfaces;


interface LaravelEssentialActionInterface
{
    /**
     * Execute action
     *
     * @param  array  $data
     * @return mixed
     */
    public function execute(array $data = []);
}
