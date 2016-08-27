<?php
namespace GeistPress\Tests\Config;

use GeistPress\Config\Config;
use GeistPress\Config\ConfigFinder;
use Mockery as m;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeistPress\Config\Config
     */
    protected $config;
    
    /**
     * @var \GeistPress\Config\ConfigFinder
     */
    protected $finderMock;
    
    public function setUp()
    {
        $this->config = new Config();
        
        $this->finderMock = m::mock(ConfigFinder::class)
            ->shouldReceive('getFiles')
            ->andReturn(['sample' => __DIR__ . DS . 'config' . DS . 'sample.php'])
            ->getMock();
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testSetFinder()
    {
        $this->config->setFinder($this->finderMock);
        
        // get access to protected $finder property to check if it's set
        $reflection = new \ReflectionClass($this->config);
        $property = $reflection->getProperty('finder');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(ConfigFinder::class, $property->getValue($this->config));
    }
    
    /**
     * @depends testSetFinder
     */
    public function testLoad()
    {
        $this->config->setFinder($this->finderMock);
        
        // no env
        $this->config->load();
        
        $this->assertEquals('bar', $this->config->get('sample.foo'));
        $this->assertEquals(['admin', 'editor'], $this->config->get('sample.access'));
        $this->assertEquals('deep', $this->config->get('sample.deep.sea.is'));
    }
}
