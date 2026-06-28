<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $modules = ['Auth', 'Classes', 'Students', 'Grades', 'Config', 'Attendance', 'Billing'];

            foreach ($modules as $module) {
                $class = 'Modules\\' . $module . '\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
                if (class_exists($class)) {
                    return $class;
                }
            }

            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }
}
