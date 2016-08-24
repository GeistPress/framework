<?php
namespace GeistPress\Foundation;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

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
     * The loaded service providers.
     * @var array
     */
    protected $loadedProviders = [];
    
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
        $this->registerProviders();
        
        // Config load
        $this['config']->load();
    }
    
    /**
     * Register a service provider with the application.
     * @param \Illuminate\Support\ServiceProvider|string $provider
     */
    public function register($provider)
    {
        // init Provider
        if (!$provider instanceof ServiceProvider) {
            $provider = new $provider($this);
        }
        
        // already loaded
        if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
            return;
        }
        
        // register provider
        $this->loadedProviders[$providerName] = true;
        $provider->register();
        $provider->boot();
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
    
        foreach ($this->paths as $key => $path) {
            $this->instance('path.'.$key, $path);
        }
    }
    
    /**
     * Register framework service providers.
     */
    protected function registerProviders()
    {
        $providers = apply_filters('geistpress_service_providers', [
            \GeistPress\Config\ConfigServiceProvider::class
        ]);
        
        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }
}
