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
		$values = (array) $value;
		$element = $this->getElementBySelector($selector);
		$options = $element->findElements(WebDriverBy::tagName('option'));

		foreach($options as $option){
			if(in_array($option->getText(), $values) || in_array($option->getAttribute('value'), $values)){
				$option->click();
			}
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

	public function submit($selector)
	{
		$this->getElementBySelector($selector)->submit();

		return $this;
	}

	public function see($text)
	{
		$body_text = $this->getElementBySelector('body')->getText();

		foreach((array) $text as $text_item){
			$this->assertContains($text_item, $body_text);
		}

		return $this;
	}

	public function notSee($text)
	{
		$body_text = $this->getElementBySelector('body')->getText();

		foreach((array) $text as $text_item){
			$this->assertNotContains($text_item, $body_text);
		}

		return $this;
	}

	public function seeOneOf($options)
	{
		$body_text = $this->getElementBySelector('body')->getText();
		$passed = false;

		foreach($options as $option){
			if(strpos($body_text, $option) !== false){
				$passed = true;
				break;
			}
		}

		static::assertThat(
			$passed,
			static::isTrue(),
			'The text "'.$body_text.'" does not contain any of the following: '.implode(', ', $options).'.'
		);

		return $this;
	}

	public function seeNumberOfElements($number, $selector)
	{
		$this->assertEquals($number, count($this->getElementsBySelector($selector)));

		return $this;
	}

	public function seeValueOfInput($value, $selector)
	{
		$element_value = $this->getElementBySelector($selector)->getAttribute('value');

		$this->assertEquals($value, $element_value);

		return $this;
	}

	public function seeOptionsAreSelected($options, $selector)
	{
		$options = (array) $options;
		$element = $this->getElementBySelector($selector);
		$selected_options = $element->findElements(WebDriverBy::cssSelector('[selected]'));
		$all_options_selected = true;

		if(!$selected_options){
			static::assertNotCount(0, $selected_options, 'No options selected for '.$selector);
		}

		foreach($options as $option){
			$option_selected = false;

			foreach($selected_options as $element){
				$selected_option_text = $element->getText();
				$selected_option_value = $element->getAttribute('value');

				if(in_array($option, [$selected_option_text, $selected_option_value])){
					$option_selected = true;
					break;
				}
			}

			static::assertThat($option_selected, static::isTrue(), 'The option, "'.$option.'" is not selected in `'.$selector.'`.');

		}

		return $this;
	}

	public function seeOptionIsSelected($option, $selector)
	{
		return $this->seeOptionsAreSelected($option, $selector);
	}

	public function seeInElement($text, $selector)
	{
		$element_text = $this->getElementBySelector($selector)->getText();

		foreach((array) $text as $text_item){
			$this->assertContains($text_item, $element_text);
		}

		return $this;
	}

	public function getCurrentUrl()
	{
		return str_replace($this->baseUrl, '', $this->session->getCurrentUrl());
	}

	public function dump($var)
	{
		dump($var);

		return $this;
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

	public function waitUntilElementVisible($selector, $timeout = 5, $interval = 200)
	{
		return $this->waitUntil(
			WebDriverExpectedCondition::visibilityOfElementLocated(
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

	protected function waitUntil($until, $timeout = 5, $interval = 200)
	{
		$this->session->wait($timeout, $interval)->until($until);

		return $this;
	}

	public function element($selector, $callback)
	{
		$element = $this->getElementBySelector($selector);

		$callback($element);

		return $this;
	}

	public function elements($selector, $callback)
	{
		$elements = $this->getElementsBySelector($selector);

		$callback($elements);

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