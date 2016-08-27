<?php
namespace GeistPress\View;

use Illuminate\View\Factory as ViewFactory;

/**
 * Class TemplateFinder
 * @package GeistPress\View
 * @method string getTaxonomyTemplate()
 * @method string getSingleTemplate()
 * @method string getCategoryTemplate()
 * @method string getTagTemplate()
 * @method string getAuthorTemplate()
 * @method string getArchiveTemplate()
 */
class TemplateFinder
{
    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;
    
    /**
     * TemplateFinder constructor.
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }
    
    /**
     * @see get_post_type_archive_template()
     * @return bool|string
     */
    public function getPostTypeArchiveTemplate()
    {
        $postType = get_query_var('post_type');
        if (is_array($postType)) {
            $postType = reset($postType);
        }
        
        $obj = get_post_type_object($postType);
        if (!$obj->has_archive) {
            return '';
        }
        
        return $this->getArchiveTemplate();
    }
    
    /**
     * @see get_embed_template()
     * @return bool|string
     */
    public function getEmbedTemplate()
    {
        $object = get_queried_object();
        $templates = ['embed'];
        
        if (!empty($object->post_type)) {
            $postFormat = get_post_format($object);
            if ($postFormat) {
                $templates[] = "embed-{$object->post_type}-{$postFormat}";
            }
            
            $templates[] = "embed-{$object->post_type}";
        }
        
        return $this->getTemplate('embed', $templates);
    }
    
    /**
     * @see get_query_template()
     * @param string $type      Filename without extension.
     * @param array  $templates An optional list of template candidates
     * @return string|bool      path to template file if it exists
     */
    public function getTemplate($type, $templates = [])
    {
        $type = preg_replace('|[^a-z0-9-]+|', '', $type);
        
        if (empty($templates)) {
            $templates = [$type];
        }
        
        foreach ($templates as $template) {
            if ($this->view->exists($template)) {
                return $template;
            }
        }
        
        return false;
    }
    
    /**
     * @see get_attachment_template()
     * @return bool|string
     */
    public function getAttachmentTemplate()
    {
        $attachment = get_queried_object();
        $templates = ['attachment'];
        
        if ($attachment) {
            if (false !== strpos($attachment->post_mime_type, '/')) {
                list($type, $subtype) = explode('/', $attachment->post_mime_type);
            } else {
                list($type, $subtype) = [$attachment->post_mime_type, ''];
            }
            
            if (!empty($subtype)) {
                $templates[] = "{$type}-{$subtype}";
                $templates[] = "{$subtype}";
            }
            $templates[] = "{$type}";
        }
        
        return $this->getTemplate('attachment', $templates);
    }
    
    /**
     * @see get_page_template()
     * @return bool|string
     */
    public function getPageTemplate()
    {
        $pid = get_queried_object_id();
        $template = get_page_template_slug();
        $pagename = get_query_var('pagename');
        
        if (!$pagename && $pid) {
            // If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
            $post = get_queried_object();
            if ($post) {
                $pagename = $post->post_name;
            }
        }
        
        $templates = [];
        if ($template && 0 === validate_file($template)) {
            $templates[] = $template;
        }
        if ($pagename) {
            $templates[] = "page-$pagename";
        }
        if ($pid) {
            $templates[] = "page-$pid";
        }
        $templates[] = 'page';
        
        return $this->getTemplate('page', $templates);
    }
    
    /**
     * Simple template lookup functions
     * @param string $name
     * @param mixed  $arguments
     * @return bool|null|string
     */
    public function __call($name, $arguments)
    {
        $methods = [
            'getTaxonomyTemplate' => [
                'type'     => 'taxonomy',
                'callable' => function ($object, $templates) {
                    if (!empty($object->slug)) {
                        $taxonomy = $object->taxonomy;
                        $templates[] = "taxonomy-$taxonomy-{$object->slug}";
                        $templates[] = "taxonomy-$taxonomy";
                    }
                    
                    return $templates;
                }
            ],
            'getSingleTemplate'   => [
                'type'     => 'single',
                'callable' => function ($object, $templates) {
                    if (!empty($object->post_type)) {
                        $templates[] = "single-{$object->post_type}-{$object->post_name}";
                        $templates[] = "single-{$object->post_type}";
                    }
                    
                    return $templates;
                }
            ],
            'getCategoryTemplate' => [
                'type'     => 'category',
                'callable' => function ($object, $templates) {
                    if (!empty($object->slug)) {
                        $templates[] = "category-{$object->slug}";
                        $templates[] = "category-{$object->term_id}";
                    }
                    
                    return $templates;
                }
            ],
            'getTagTemplate'      => [
                'tag'      => 'taxonomy',
                'callable' => function ($object, $templates) {
                    if (!empty($object->slug)) {
                        $templates[] = "tag-{$object->slug}";
                        $templates[] = "tag-{$object->term_id}";
                    }
                    
                    return $templates;
                }
            ],
            'getAuthorTemplate'   => [
                'tag'      => 'author',
                'callable' => function ($object, $templates) {
                    if ($object instanceof \WP_User) {
                        $templates[] = "author-{$object->user_nicename}";
                        $templates[] = "author-{$object->ID}";
                    }
                    
                    return $templates;
                }
            ],
            'getArchiveTemplate'  => [
                'tag'      => 'archive',
                'callable' => function ($object, $templates) {
                    $postTypes = array_filter((array)get_query_var('post_type'));
                    if (count($postTypes) == 1) {
                        $postType = reset($postTypes);
                        $templates[] = "archive-{$postType}";
                    }
                    
                    return $templates;
                }
            ]
        ];
        
        if (array_key_exists($name, $methods)) {
            return $this->getTemplate($methods[$name]['type'], $methods[$name]['callable']);
        }
        
        return null;
    }
    
    /**
     * Simple Template lookup by type
     * @param string   $type
     * @param \Closure $addTemplates
     * @return bool|string
     */
    protected function getTemplateByType($type, \Closure $addTemplates)
    {
        return $this->getTemplate($type, call_user_func($addTemplates, get_queried_object(), [$type]));
    }
}
