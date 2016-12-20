Laravel JTest is a package for running automated browser tests again a Laravel based application. It was specifically designed to be able to test Javascript enabled code.

## Installation

```
composer require pstephan1187/laravel-jtest
```

Then in your PHPUnit tests, extend the `JTest\JTestCase` class and set the `baseUrl` property to your application url:

```
<?php

use JTest\JTestCase;

class MyTestClass extends JTestCase
{
	protected $baseUrl = 'http://localhost:8888';

	//...
}
```

You can also set the browser to test with as well (defaults to Chrome):

```
protected $browser = 'firefox';
```

You will also need to make sure that you have Selenium Stand-Alone Server installed and running. You can download Selenium [here](http://docs.seleniumhq.org/download/ "Download Selenium"). You can run selenium by executing this command in the directory that you have the selenium server binary:

```
java -jar selenium-server-standalone-x.x.x.jar
```

Make sure you use the actual version numbers for your download.

## Usage

JTest uses a chainable method structure. This allows to to continually run each method in sequence. Here is a Basic example of how to use JTest:

```
public function testUserCanLogin()
{
	$this->visit('/')
	     ->click('Login')
	     ->seePageIs('/auth/login')
	     ->type('user.email@example.com', '#email')
	     ->type('user_p@ssw0rd', '#password')
	     ->press('form#login input[type=submit]')
	     ->seePageIs('/dashboard');
}
```

## Methods

### setWindowSize($width, $height)

This allows you to force the size of the browser window to certain dimensions. Both width and height are required and must be pixel values. The browser will be resized the next time a window is opened. It is recommended that you run this command before you run the `visit` command so that the window is immediately sized as you need.

### visit($uri)

Will navigate the browser to the given path. The `baseUrl` is prepended to the `$uri`. If you have the `baseUrl` set to 'http://localhost:8000' and then run `$this->visit('/my-page')`, the browser will navigate to 'http://localhost:8000/my-page'.

### seePageIs($url)

This will assert that the current url is the `baseUrl` plus the given url. Given that the current URL is 'http://localhost:8000/my-page' and the `baseUrl` is 'http://localhost:8000', then `seePageIs('/my-page')` will pass.

### type($text, $selector)

This will type the given text into the first element found matching the given CSS selector.

### press($selector)

This will execute a click event on the first element found matching the given CSS selector.

### click($text)

This will execute a click event on the first hyperlink ("a" tag) with the matching text.

### select($values, $selector)

This will select any options whose text or value match any of the given values for the first element found matching the given CSS selector. `$values` can be an array or a single item.

### file($file_path, $selector)

This will select the file located at `$file_path` in the first file input found matching the given CSS selector.

### submit($selector)

This will submit the first form found matching the given CSS selector.

### see($text)

This will assert that the given text is visible on the page.

### notSee($text)

This will assert that the given text is not visible on the page.

### seeOneOf($text_options)

This will assert that at least one of the given text options is visible on the page.

### seeNumberOfElements($number, $selector)

This will assert that the given CSS selector returns the given number of elements

### seeValueOfInput($value, $selector)

This will assert that the first input found matching the given CSS selector has a value attribute that matches the given value.

### seeOptionsAreSelected($options, $selector)

This will assert that each of the given options are selected in the first element found matching the given CSS selector. `$options` will be matched base on the text of the option element or its value. `$options` can accept an array of items or a singular item.

### seeOptionIsSelected($option, $selector)

This aliases to `seeOptionsAreSelected`

### seeInElement($text, $selector)

This asserts that the first element found matching the given CSS selector contains the given text. If `$text` is an array, this will assert that all given items are found within the matching element.

### getCurrentUrl()

This will return the URL that the browser is currently at. This method does not allow further chaining.

### dump($var)

This will dump the given variable to the console.

### wait($seconds)

This will pause the execution of the test for the given number of seconds

### waitUntilElementExists($selector, $timeout = 5, $interval = 200)

This will pause the execution of the test until an element matching the given selector exists. The element will be checked for every `$interval` milliseconds until `$timeout` seconds has passed.

### waitUntilElementVisible($selector, $timeout = 5, $interval = 200)

This will pause the execution of the test until an element matching the given selector is visible. The element will be checked for every `$interval` milliseconds until `$timeout` seconds has passed.

### waitUntilText($text, $timeout = 5, $interval = 200)

This will pause the execution of the test until the given text exists anywhere on the page. The text will be checked for every `$interval` milliseconds until `$timeout` seconds has passed.

### element($selector, $callback)

This will find the first element found matching the given CSS selector, pass it to the callback, execute the callback, then continue with the test. The following example finds the first `h1` tag, then searches it for a button, then clicks the button.

```
$this->element('h1', function($element){
	$element->findElement('button')->click()
});
```

The `$element` that is passed to the callback is of type `Facebook\WebDriver\WebDriverElement`. [Read more here](https://github.com/facebook/php-webdriver/blob/community/lib/Remote/RemoteWebElement.php "Facebook WebDriverElement class").

### elements($selector, $callback)

Acts the same as `element` but passes all matching elements to the callback instead of just the first one.