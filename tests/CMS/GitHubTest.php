<?php
use Facebook\WebDriver\WebDriverCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
//require_once 'vendor/symfony/yaml/Symfony/Component/Yaml/Tests/DumperTest.php';

/**
 * A test case.
 */
class GitHubTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \RemoteWebDriver
     */
    protected $webDriver;

    /**
     *
     * @var string
     */
    protected $url = "https://github.com";

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // TODO Auto-generated GitHubTest::setUp()
        
        $capabilities = array(
            WebDriverCapabilityType::BROWSER_NAME => "firefox"
        );
        $this->webDriver = RemoteWebDriver::create("http://localhost:4444/wd/hub", $capabilities);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated GitHubTest::tearDown()
        // $this->webDriver->close();
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * 测试打开一个链接
     */
    /*
     * public function testGitHubHome()
     * {
     * $this->webDriver->get($this->url);
     * $this->assertContains("GitHub", $this->webDriver->getTitle());
     * }
     */
    
    /**
     * 测试在打开的链接中搜索
     */
    public function testSearch()
    {
        $this->webDriver->get($this->url);
        $search = $this->webDriver->findElement(WebDriverBy::className('js-site-search-focus'));
        $search->click();
        
        $this->webDriver->getKeyboard()->sendKeys("php-webdriver");
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        
        $firstResult = $this->webDriver->findElement(WebDriverBy::cssSelector('li.public:nth-child(1) > h3 > a > em'));
        $firstResult->click();
        
        $this->assertContains("php-webdriver", $this->webDriver->getTitle());
        
     //   $this->assertEquals('https://github.org/facebook/php-webdriver', $this->webDriver->getCurrentURL());
        
        $this->assertElementNotFound(WebDriverBy::className('avatar'));
    }

    protected function waitForUserInput()
    {
        if (trim(fgets(fopen("php://stdin", "R"))) != chr(13))
            return;
    }

    protected function assertElementNotFound($by)
    {
        $els = $this->webDriver->findElements($by);
        if (count($els)) {
            $this->fail("Unexpectedly element was found");
        }
        $this->assertTrue(true);
    }
}

