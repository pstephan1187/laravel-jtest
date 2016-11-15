<?php

namespace JTest;
use Illuminate\Foundation\Testing\ApplicationTrait;
// use Illuminate\Foundation\Testing\AssertionsTrait;
// use Illuminate\Foundation\Testing\CrawlerTrait;

use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class JTestCase extends \PHPUnit_Framework_TestCase
{
	use ApplicationTrait,
		// AssertionsTrait,
		// CrawlerTrait,
		InteractsWithApplicationTrait;

	protected $browser = 'chrome';
	protected $baseUrl = 'http://localhost:8000';
	protected $seleniumPort = 4444;
    protected $beforeApplicationDestroyedCallbacks = [];

	protected $session;
	protected $browser_width;
	protected $browser_height;

	public function setUp()
	{
	    parent::setUp();
	    $this->refreshApplication();
	}

    public function createApplication()
    {
        $app = require(realpath('./bootstrap/app.php'));

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

	public function closeBrowser()
	{
	    if ($this->session) {
	        $this->session->quit();
	    }
	}

	public function newSession()
	{
		$host = 'http://localhost:'.$this->seleniumPort.'/wd/hub';
		$browser = $this->browser;

		$this->session = RemoteWebDriver::create(
			$host,
			DesiredCapabilities::$browser()
		);

		return $this->session;
	}

	public function setWindowSize($width, $height)
	{
		$this->browser_width = $width;
		$this->browser_height = $height;

		$this->resizeWindow();

		return $this;
	}

	protected function resizeWindow()
	{
		if($this->browser_width && $this->browser_height && $this->session){
			$this->session->manage()->window()->setSize(
				new WebDriverDimension($this->browser_width, $this->browser_height)
			);
		}
	}

	protected function beforeApplicationDestroyed(callable $callback)
	{
	    $this->beforeApplicationDestroyedCallbacks[] = $callback;
	}

	public function tearDown()
	{
		$this->closeBrowser();

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
		    \Mockery::close();
		}
	}
}