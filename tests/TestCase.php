<?php

use Illuminate\Support\Facades\Artisan;

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

        /* Exclude specific models */
        $files = array_diff($files, ['App\Permission']);

        /* Reset each model's event listeners */
        foreach ($files as $model) {
            call_user_func([$model, 'flushEventListeners']);
            call_user_func([$model, 'boot']);
        }
    }

    /**
     * Start a session and attach the CSRF token to the attributes.
     *
     * @return array
     */
    protected function csrf(array $attributes = [])
    {
        $this->startSession();

        return $attributes + ['_token' => csrf_token()];
    }
}
