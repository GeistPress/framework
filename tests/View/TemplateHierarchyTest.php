<?php
namespace GeistPress\Tests\View;

use GeistPress\View\TemplateFinder;
use GeistPress\View\TemplateHierarchy;
use Illuminate\View\Factory;
use Mockery as m;
use phpmock\mockery\PHPMockery;

/**
 * Class TemplateHierarchyTest
 * @package GeistPress\Tests\View
 */
class TemplateHierarchyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeistPress\View\TemplateHierarchy;
     */
    protected $template;
    
    /**
     * @var \GeistPress\View\TemplateHierarchy
     */
    protected $finder;
    
    protected $factory;
    
    public function setUp()
    {
        $this->factory = m::mock(Factory::class);
        $this->finder = m::mock(TemplateFinder::class);
        
        $this->template = new TemplateHierarchy($this->factory, $this->finder);
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @dataProvider makeProvider
     * @param string $func   is_* function to mock
     * @param string $method get template method to mock
     * @param string $return return template
     */
    public function testMake($func, $method, $return)
    {
        foreach ($this->makeProvider() as $item) {
            if ($item[0] === $func) {
                PHPMockery::mock('GeistPress\View', $func)->andReturn(true);
                continue;
            }
            PHPMockery::mock('GeistPress\View', $item[0])->andReturn(false);
        }
        
        $this->finder->shouldReceive('getTemplate')->andReturn($return);
        $this->finder->shouldReceive($method)->andReturn($return);
        $this->factory->shouldReceive('make')->once()->with($return)->andReturnSelf();
        $this->factory->shouldReceive('render')->once()->withNoArgs()->andReturn('Output: ' . $return);
        
        $this->expectOutputString('Output: ' . $return);
        $this->template->make('');
    }
    
    public function makeProvider()
    {
        return [
            'embed template'             => ['is_embed', 'getEmbedTemplate', 'embed'],
            '404 template'               => ['is_404', 'getTemplate', '404'],
            'search template'            => ['is_search', 'getTemplate', 'search'],
            'front_page template'        => ['is_front_page', 'getTemplate', 'front_page'],
            'home template'              => ['is_home', 'getTemplate', 'home'],
            'post_type_archive template' => ['is_post_type_archive', 'getPostTypeArchiveTemplate', 'post_type_archive'],
            'tax template'               => ['is_tax', 'getTaxonomyTemplate', 'tax'],
            'attachment template'        => ['is_attachment', 'getAttachmentTemplate', 'attachment'],
            'single template'            => ['is_single', 'getSingleTemplate', 'single'],
            'page template'              => ['is_page', 'getPageTemplate', 'page'],
            'singular template'          => ['is_singular', 'getTemplate', 'singular'],
            'category template'          => ['is_category', 'getCategoryTemplate', 'category'],
            'tag template'               => ['is_tag', 'getTagTemplate', 'tag'],
            'author template'            => ['is_author', 'getAuthorTemplate', 'author'],
            'date template'              => ['is_date', 'getTemplate', 'date'],
            'archive template'           => ['is_archive', 'getArchiveTemplate', 'archive'],
            'paged template'             => ['is_paged', 'getTemplate', 'paged'],
            'index template'             => ['time', 'getTemplate', 'index']
        ];
    }
    
    public function testMakeWPTemplate()
    {
        foreach ($this->makeProvider() as $item) {
            PHPMockery::mock(__NAMESPACE__, $item[0])->andReturn(false);
        }
        
        $this->finder->shouldReceive('getTemplate')->with('index')->andReturn(false);
        
        $this->assertEquals('foo', $this->template->make('foo'));
    }
}
