<?php

namespace JTest;
use Illuminate\Foundation\Testing\ApplicationTrait;
use Illuminate\Foundation\Testing\AssertionsTrait;
use Illuminate\Foundation\Testing\CrawlerTrait;

class JTestCase extends PHPUnit_Extensions_Selenium2TestCase
{
	use ApplicationTrait, AssertionsTrait, CrawlerTrait;

	protected $browser = 'chrome';
	protected $baseUrl = 'http://localhost:8000';
	protected $seleniumPort = 4444;
    protected $beforeApplicationDestroyedCallbacks = [];

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setup()
    {
        $this->setBrowser($this->browser);
        $this->setBrowserUrl($this->baseUrl);
        $this->setPort($this->seleniumPort);

        if (! $this->app) {
            $this->refreshApplication();
        }
    }
    
    protected function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }

    public function tearDown()
    {
        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                call_user_func($callback);
            }

            $this->app->flush();

            $this->app = null;
        }

        if (property_exists($this, 'serverVariables')) {
            $this->serverVariables = [];
        }

        if (class_exists('Mockery')) {
            Mockery::close();
        }
    }

}