<?php

use Illuminate\Support\Facades\Artisan;
use League\FactoryMuffin\Facade as FactoryMuffin;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Run before each test class.
     */
    public static function setUpBeforeClass()
    {
        FactoryMuffin::loadFactories(__DIR__.'/factories');
    }

    /**
     * Run before each test.
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        $this->resetEvents();
    }

    /**
     * Run after each test.
     */
    public function tearDown()
    {
        Artisan::call('migrate:reset');
    }

    /**
     * Flush event listeners on all models.
     */
    private function resetEvents()
    {
        /* Get all model files */
        $path_to_models = __DIR__.'/../app';
        $files = glob($path_to_models.'/*.php');

        /* Convert the filename to the namespaced model */
        $files = str_replace($path_to_models.'/', 'App\\', $files);
        $files = str_replace('.php', '', $files);

        /* Reset each model's event listeners */
        foreach ($files as $model) {
            call_user_func([$model, 'flushEventListeners']);
            call_user_func([$model, 'boot']);
        }
    }
}
