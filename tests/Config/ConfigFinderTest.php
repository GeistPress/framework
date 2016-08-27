<?php
namespace GeistPress\Tests\Config;

use GeistPress\Config\ConfigFinder;

class ConfigFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeistPress\Config\ConfigFinder
     */
    protected $finder;
    
    public function setUp()
    {
        $this->finder = new ConfigFinder(__DIR__ . '/config/');
    }
    
    public function testGetFiles()
    {
        $files = $this->finder->getFiles();
        
        // Check returned values.
        $this->assertTrue(is_array($files));
        $this->assertEquals([
            'sample' => __DIR__ . DS . 'config' . DS . 'sample.php'
        ], $files);
    }
    
    public function testGetFilesWithEnv()
    {
        $files = $this->finder->getFiles('local');
        
        // Check returned values.
        $this->assertTrue(is_array($files));
        $this->assertEquals([
            'sample' => __DIR__ . DS . 'config' . DS . 'local' . DS . 'sample.php'
        ], $files);
    }
    
    public function testGetFilesFromNonExistingDirectory()
    {
        $finder = new ConfigFinder('thiisdoesnotexist');
        $files = $finder->getFiles();
        
        // Check returned values.
        $this->assertTrue(is_array($files));
        $this->assertTrue(empty($files));
    }
}
