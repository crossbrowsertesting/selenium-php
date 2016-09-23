<?php

// php composer.phar require facebook/webdriver

use Facebook\WebDriver\Remote\RemoteWebdriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedConditions;

// composer global require phpunit/phpunit

class ToDoTest extends PHPUnit_Framework_TestCase {
    protected $driver;

	public function setUp() {
        $username = "you@yourdomain.com";
        $authkey = "12345";
        $capabilities = array("name"=> "Selenium Test Example",
										 "build"=> "1.0",
										 "browser_api_name"=> "FF46",
										 "os_api_name"=> "Win10",
										 "record_video"=> "true",
										 "take_snapshot"=> "true",
										 "record_network"=> "true"
										 );
        $host = "http://" . $username . ":" . $authkey . "@hub.crossbrowsertesting.com:80/wd/hub";
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testToDos() {
    	try {
	    	echo "Navigating to URL\n";
	        $this->driver->get("http://crossbrowsertesting.github.io/todo-app.html");

	        sleep(3);
	        echo "Clicking Checkbox\n";
	        $this->driver->findElement(WebDriverBy::name("todo-4"))->click();
	        echo "Clicking Checkbox\n";
	        $this->driver->findElement(WebDriverBy::name("todo-5"))->click();

	        $elems = $this->driver->findElements(WebDriverBy::className("done-true"));
	        $this->assertEquals(2, count($elems));

	        echo "Entering Text\n";
	        $this->driver->findElement(WebDriverBy::id("todotext"))->sendKeys("Run your first Selenium test");

	        echo "Adding todo to the list\n";
	        $this->driver->findElement(WebDriverBy::id("addbutton"))->click();

	        $spanText = $this->driver->findElement(WebDriverBy::xpath("/html/body/div/div/div/ul/li[6]/span"))->getText();
	        $this->assertEquals("Run your first Selenium test", $spanText);

	        echo "Archiving old todos\n";
	        $this->driver->findElement(WebDriverBy::linkText("archive"))->click();

	        $elems = $this->driver->findElements(WebDriverBy::className("done-false"));
	        $this->assertEquals(4, count($elems));

	        // if we've made it this far, assertions have passed and we'll set the score to pass.
	    } catch (Exception $ex) {
	    	// if we caught an exception along the way, an assertion failed and we'll set the score to fail.
	    	echo "Caught Exception: " . $ex->getMessage();

	    }
    }

    public function tearDown() {
    	$this->driver->quit();
    }

}
?>
