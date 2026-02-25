<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $configCache = __DIR__ . '/../bootstrap/cache/config.php';
        if (file_exists($configCache)) {
            @unlink($configCache);
        }
        
        $app = parent::createApplication();
        
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        
        return $app;
    }
}
