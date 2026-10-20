<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");
        if ($connection !== 'sqlite' && !preg_match('/(?:_test|_testing)$/', $database)) {
            throw new \RuntimeException('Tests require a dedicated database ending in _test or _testing.');
        }
        return $app;
    }
}
