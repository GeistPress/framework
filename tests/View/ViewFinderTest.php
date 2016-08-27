<?php
namespace GeistPress\Tests\View;

use GeistPress\View\ViewFinder;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

/**
 * Class ViewFinderTest
 * @package GeistPress\Tests\View
 */
class ViewFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeistPress\View\ViewFinder
     */
    protected $finder;
    
    public function setUp()
    {
        $filesystem = m::mock(Filesystem::class);
        $this->finder = new ViewFinder($filesystem, [__DIR__], ['twig', 'php']);
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testGetViews()
    {
        $this->assertTrue(is_array($this->finder->getViews()));
    }
}
