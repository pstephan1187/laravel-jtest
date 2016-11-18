<?php

namespace JTest;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverExpectedCondition;

trait InteractsWithApplicationTrait
{
	public function visit($uri)
	{
	    $uri = $this->baseUrl.$uri;

	    if(!$this->session){
	    	$this->newSession();
	    }
	    
	    $this->resizeWindow();

	    $this->session->get($uri);

	    return $this;
	}

	public function seePageIs($url)
	{
		$currentUri = str_replace($this->baseUrl, '', $this->session->getCurrentUrl());
		$this->assertEquals($url, $currentUri);

		return $this;
	}

	public function type($text, $selector)
	{
		$element = $this->getElementBySelector($selector);
		$element->sendKeys($text);

		return $this;
	}

	public function press($selector)
	{
		$element = $this->getElementBySelector($selector);
		$element->click();

		return $this;
	}

	public function click($text)
	{
		$element = $this->session->findElement(WebDriverBy::linkText($text));
		$element->click();

		return $this;
	}

	public function select($value, $selector)
	{

		$element = $this->getElementBySelector($selector);
		$options = $element->findElements(WebDriverBy::tagName('option'));

		foreach($options as $option){
			if($option->getText() == $value || $option->getAttribute('value') == $value){
				$option->click();
			}
		}

		return $this;
	}

	public function submit($selector)
	{
		$this->getElementBySelector($selector)->submit();

		return $this;
	}

	public function see($text)
	{
		$body_text = $this->getElementBySelector('body')->getText();

		$this->assertContains($text, $body_text);

		return $this;
	}

	public function seeOneOf($options)
	{
		$body_text = $this->getElementBySelector('body')->getText();
		$failed = true;

		foreach($options as $option){
			if(strpos($body_text, $option) !== false){
				$failed = false;
				break;
			}
		}

		if($failed){
			$error =
				'The text '.
				'"'.$body_text.'" '.
				'does not contain any of the following: '.
				implode(', ', $options).'.';

			throw new \PHPUnit_Framework_ExpectationFailedException($error);
		}

		return $this;
		
	}

	public function file($file_path, $selector)
	{
		$element = $this->getElementBySelector($selector);
		$element->setFileDetector(new LocalFileDetector());
		$element->sendKeys($file_path);

		return $this;
	}

	public function getCurrentUrl()
	{
		return str_replace($this->baseUrl, '', $this->session->getCurrentUrl());
	}

	public function seeNumberOfElements($number, $selector)
	{
		$this->assertEquals($number, count($this->elements($selector)));

		return $this;
	}

	public function seeValueOfInput($value, $selector)
	{
		$element_value = $this->getElementBySelector($selector)->getAttribute('value');

		$this->assertEquals($value, $element_value);

		return $this;
	}

	public function seeOptionIsSelected($option, $selector)
	{
		$element = $this->getElementBySelector($selector);
		$selected_option = $element->findElement(WebDriverBy::cssSelector('[selected]'));

		if(!$selected_option){
			throw new \PHPUnit_Framework_ExpectationFailedException('No options selected for '.$selector);
		}

		$this->assertEquals($option, $selected_option->getText());

		return $this;
	}

	public function element($selector)
	{
		return $this->getElementBySelector($selector);
	}

	public function elements($selector)
	{
		return $this->getElementsBySelector($selector);
	}

	public function wait($seconds)
	{
		sleep($seconds);

		return $this;
	}

	public function waitUntilElementExists($selector, $timeout = 5, $interval = 200)
	{
		return $this->waitUntil(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::cssSelector($selector)
			)
		);
	}

	public function waitUntilText($text, $timeout = 5, $interval = 200)
	{
		return $this->waitUntil(
			WebDriverExpectedCondition::textToBePresentInElement(
				WebDriverBy::tagName('body'),
				$text
			)
		);
	}

	public function waitUntil($until, $timeout = 5, $interval = 200)
	{
		$this->session->wait($timeout, $interval)->until($until);

		return $this;
	}

	protected function getElementBySelector($selector)
	{
		return $this->session->findElement(WebDriverBy::cssSelector($selector));
	}

	protected function getElementsBySelector($selector)
	{
		return $this->session->findElements(WebDriverBy::cssSelector($selector));
	}
}