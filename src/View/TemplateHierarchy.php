<?php
namespace GeistPress\View;

use Illuminate\View\Factory as ViewFactory;

/**
 * Class TemplateHierarchy
 * @package GeistPress\View
 */
class TemplateHierarchy
{
    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;
    
    /**
     * @var \GeistPress\View\TemplateFinder
     */
    protected $finder;
    
    /**
     * TemplateHierarchy constructor.
     * @param \Illuminate\View\Factory        $view
     * @param \GeistPress\View\TemplateFinder $finder
     */
    public function __construct(ViewFactory $view, TemplateFinder $finder)
    {
        $this->view = $view;
        $this->finder = $finder;
    }
    
    /**
     * Make template
     * @see https://core.trac.wordpress.org/browser/tags/4.5.3/src/wp-includes/template
     * @param string $template
     * @return mixed
     */
    public function make($template)
    {
        // get twig template exists e.g. page.twig
        $file = null;
        if (is_embed() && $file = $this->finder->getEmbedTemplate()) :
        elseif (is_404() && $file = $this->finder->getTemplate('404')) :
        elseif (is_search() && $file = $this->finder->getTemplate('search')) :
        elseif (is_front_page() && $file = $this->finder->getTemplate('front_page', ['front-page'])) :
        elseif (is_home() && $file = $this->finder->getTemplate('home', ['home', 'index'])) :
        elseif (is_post_type_archive() && $file = $this->finder->getPostTypeArchiveTemplate()) :
        elseif (is_tax() && $file = $this->finder->getTaxonomyTemplate()) :
        elseif (is_attachment() && $file = $this->finder->getAttachmentTemplate()) :
            remove_filter('the_content', 'prepend_attachment');
        elseif (is_single() && $file = $this->finder->getSingleTemplate()) :
        elseif (is_page() && $file = $this->finder->getPageTemplate()) :
        elseif (is_singular() && $file = $this->finder->getTemplate('singular')) :
        elseif (is_category() && $file = $this->finder->getCategoryTemplate()) :
        elseif (is_tag() && $file = $this->finder->getTagTemplate()) :
        elseif (is_author() && $file = $this->finder->getAuthorTemplate()) :
        elseif (is_date() && $file = $this->finder->getTemplate('date')) :
        elseif (is_archive() && $file = $this->finder->getArchiveTemplate()) :
        elseif (is_paged() && $file = $this->finder->getTemplate('paged')) :
        else :
            $file = $this->finder->getTemplate('index');
        endif;
        
        if ($file) {
            echo $this->view->make($file)->render();
            
            return '';
        }
        
        return $template;
    }
}
