<?php
namespace GeistPress\Tests\Config;

use GeistPress\Config\Config;
use GeistPress\Config\ConfigFinder;
use GeistPress\Config\ConfigServiceProvider;
use GeistPress\Foundation\Application;
use Illuminate\Container\Container;
use Mockery as m;

class ConfigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeistPress\Foundation\Application
     */
    protected $app;
    
    /**
     * @var \GeistPress\Config\ConfigServiceProvider
     */
    protected $service;
    
    public function setUp()
    {
        // bind container to application so we can use the container and
        // don't have to mock the singleton method
        $container = new Container();
        $container->bind(Application::class, Container::class);
        
        // init
        $this->app = $container->make(Application::class);
        $this->service = new ConfigServiceProvider($this->app);
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testRegister()
    {
        $this->app['path.config'] = __DIR__ . DS . 'config';
        
        $this->assertNull($this->service->register());
        $this->assertInstanceOf(ConfigFinder::class, $this->app['config.finder']);
        $this->assertInstanceOf(Config::class, $this->app['config']);
    }
}
