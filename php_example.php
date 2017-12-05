<?php
// php composer.phar require facebook/webdriver
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedConditions;
use PHPUnit\Framework\TestCase;
// composer global require phpunit/phpunit
class ToDoTest extends TestCase {
    protected $username = "";
    protected $authkey = "";
    private $seleniumTestId="";
    protected $driver;    
    public $ch;

    public function getSeleniumTestId() {
        $url = "https://crossbrowsertesting.com/api/v3/selenium?format=json&num=1&active=true";
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->authkey");
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $json = json_decode($result);
        $this->seleniumTestId = $json->selenium[0]->selenium_test_id;
    }        


    public function takeScreenShot() {
        $url = 'https://crossbrowsertesting.com/api/v3/selenium/' . $this->seleniumTestId . '/snapshots';

        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->authkey");
        
        $result = curl_exec($ch);
        $jsonData = json_decode($result);
    }

    public function setUp() {

        $capabilities = array("name"=> "Selenium Test Example",
                              "build"=> "1.0",
                              "browserName" => "Firefox",
                              "version" => "55x64",
                              "platform" => "Windows 10",
                              "record_video"=> "true",
                              "take_snapshot"=> "true",
                              "record_network"=> "true");
        
        $host = "http://" . $this->username . ":" . $this->authkey . "@hub.crossbrowsertesting.com:80/wd/hub";
        $this->driver = RemoteWebDriver::create($host, $capabilities, 100000, 100000 );
        $this->getSeleniumTestId();
    }
    
    public function testToDos() {
        try {
            print "\nNavigating to URL\n";
            $this->driver->get("http://croSsbrowsertesting.github.io/todo-app.html");

            sleep(3);
            $this->takeScreenShot();
            print "Clicking Checkbox\n";
            $this->driver->findElement(WebDriverBy::name("todo-4"))->click();
            $this->takeScreenShot();
            
            print "Clicking Checkbox\n";
            $this->driver->findElement(WebDriverBy::name("todo-5"))->click();
            $this->takeScreenShot();            
            $elems = $this->driver->findElements(WebDriverBy::className("done-true"));
            $this->assertEquals(2, count($elems));
            
            print "Entering Text\n";
            $this->driver->findElement(WebDriverBy::id("todotext"))->sendKeys("Run your first Selenium test");
            $this->takeScreenShot();
            
            print "Adding todo to the list\n";
            $this->driver->findElement(WebDriverBy::id("addbutton"))->click();
            $this->takeScreenShot();

            $spanText = $this->driver->findElement(WebDriverBy::xpath("/html/body/div/div/div/ul/li[6]/span"))->getText();
            $this->assertEquals("Run your first Selenium test", $spanText);
            
            print "Archiving old todos\n";
            $this->driver->findElement(WebDriverBy::linkText("archive"))->click();
            $this->takeScreenShot();
            $elems = $this->driver->findElements(WebDriverBy::className("done-false"));
            $this->assertEquals(4, count($elems));
            // if we've made it this far, assertions have passed and we'll set the score to pass.
        } catch (Exception $ex) {
            // if we caught an exception along the way, an assertion failed and we'll set the score to fail.
            print "Caught Exception: " . $ex->getMessage();
        }
    }
    public function tearDown() {
        $this->driver->quit();
    }
}
?>