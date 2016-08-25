<?php
namespace GeistPress\View;

use Illuminate\View\Engines\PhpEngine;
use Twig_Environment;

/**
 * Class TwigEngine
 * @package GeistPress\View
 */
class TwigEngine extends PhpEngine
{
    /**
     * @var Twig_Environment
     */
    protected $environment;
    
    /**
     * @var \GeistPress\View\ViewFinder
     */
    protected $finder;
    
    /**
     * @var string
     */
    protected $extension = '.twig';
    
    /**
     * TwigEngine constructor.
     * @param \Twig_Environment           $environment
     * @param \GeistPress\View\ViewFinder $finder
     */
    public function __construct(Twig_Environment $environment, ViewFinder $finder)
    {
        $this->environment = $environment;
        $this->finder = $finder;
    }
    
    /**
     * Return the evaluated template.
     * @param string $path The file name with its file extension.
     * @param array  $data Template data (view data)
     * @return string
     */
    public function get($path, array $data = [])
    {
        $file = array_search($path, $this->finder->getViews());
        $file = str_replace('.', DS, $file);
        $template = $this->environment->loadTemplate($file . $this->extension);
        
        return $template->render($data);
    }
}
