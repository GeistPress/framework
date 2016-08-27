<?php
namespace GeistPress\Tests\View;

use GeistPress\View\TwigEngine;
use GeistPress\View\ViewFinder;
use Mockery as m;

/**
 * Class TwigEngineTest
 * @package GeistPress\Tests\View
 */
class TwigEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeistPress\View\TwigEngine
     */
    protected $engine;
    
    public function setUp()
    {
        $loader = m::mock(\Twig_Template::class)
            ->shouldReceive('render')
            ->andReturn('rendered')
            ->getMock();
        $env = m::mock(\Twig_Environment::class)
            ->shouldReceive('loadTemplate')
            ->with('sample.twig')
            ->andReturn($loader)
            ->getMock();
        $finder = m::mock(ViewFinder::class)
            ->shouldReceive('getViews')
            ->once()
            ->andReturn(['sample' => __DIR__ . DS . 'views' . DS . 'sample.twig'])
            ->getMock();
        $this->engine = new TwigEngine($env, $finder);
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testGet()
    {
        $this->assertEquals('rendered', $this->engine->get(__DIR__ . DS . 'views' . DS . 'sample.twig'));
    }
}
