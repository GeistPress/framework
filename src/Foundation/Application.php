<?php
namespace GeistPress\Foundation;

use Illuminate\Container\Container;

/**
 * Class Application
 * @package GeistPress\Foundation
 */
class Application extends Container
{
    /**
     * Project paths
     * @var array
     */
    protected $paths = [];
    
    /**
     * Directory of the theme
     * @var null|string
     */
    protected $themeDir = null;
    
    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->instance('app', $this);
    }
    
    /**
     * Setup App.
     * @param string|null $themeDir
     */
    public function setup($themeDir = null)
    {
        $this->themeDir = $themeDir;
        $this->setupPaths();
    }
    
    /**
     * Initialize the paths.
     */
    protected function setupPaths()
    {
        $this->paths = [
            'core'    => __DIR__ . DS . '..' . DS,
            'storage' => GEISTPRESS_STORAGE,
            'config'  => $this->themeDir . DS . 'resources' . DS . 'config' . DS,
        ];
    }
}
