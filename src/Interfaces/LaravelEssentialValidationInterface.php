<?php


namespace Lagumen\LaravelEssential\Interfaces;


interface LaravelEssentialValidationInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function save(array $data = []);

    /**
     * @param array $data
     * @return array
     */
    public function update(array $data = []);
}
