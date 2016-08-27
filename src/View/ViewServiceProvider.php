<?php
namespace GeistPress\View;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider as IlluminateViewServiceProvider;
use InvalidArgumentException;

/**
 * Class ViewServiceProvider
 * @package GeistPress\View
 */
class ViewServiceProvider extends IlluminateViewServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerOptions();
        $this->registerEngines();
        $this->registerFactory();
    }
    
    /**
     * Register Twig config option bindings
     */
    protected function registerOptions()
    {
        // options
        $this->app->bindIf('twig.options', function () {
            $options = $this->app['config']->get('twig.environment', []);
            
            // set default cache path
            if (!isset($options['cache']) || is_null($options['cache'])) {
                $options['cache'] = $this->app['path.storage'] . 'twig';
            }
            
            return $options;
        });
        
        // extensions
        $this->app->bindIf('twig.extensions', function () {
            $load = $this->app['config']->get('twig.extensions.enabled', []);
            
            // Is debug enabled?
            $options = $this->app['twig.options'];
            $isDebug = (bool)(isset($options['debug'])) ? $options['debug'] : false;
            
            if ($isDebug || (defined('WP_DEBUG') && WP_DEBUG)) {
                array_unshift($load, 'Twig_Extension_Debug');
            }
            
            return $load;
        });
    }
    
    /**
     * Register twig loader and engine
     */
    protected function registerEngines()
    {
        $container = $this->app;
        
        // Twig Filesystem loader.
        $this->app->singleton('twig.loader', function () {
            return new \Twig_Loader_Filesystem();
        });
        
        // Twig Environment
        $this->app->singleton('twig', function ($container) {
            $extensions = $this->app['twig.extensions'];
            $twig = new \Twig_Environment($container['twig.loader'], $container['twig.options']);
            
            // Instantiate and add extensions
            foreach ($extensions as $extension) {
                // Get an instance of the extension
                if (is_string($extension)) {
                    try {
                        $extension = $this->app->make($extension);
                    } catch (\Exception $e) {
                        throw new InvalidArgumentException(
                            "Cannot instantiate Twig extension '$extension': " . $e->getMessage()
                        );
                    }
                } elseif (is_callable($extension)) {
                    $extension = $extension($this->app, $twig);
                } elseif (!is_a($extension, 'Twig_Extension')) {
                    throw new InvalidArgumentException('Incorrect extension type');
                }
                
                $twig->addExtension($extension);
            }
            
            return $twig;
        });
        
        // register engine resolvers
        $this->app->singleton('view.engine.resolver', function () use ($container) {
            $resolver = new EngineResolver();
            
            // register php engine
            $resolver->register('php', function () {
                return new PhpEngine();
            });
            
            // register twig engine
            $resolver->register('twig', function () use ($container) {
                // set paths
                $container['twig.loader']->setPaths($container['view.finder']->getPaths());
                
                return new TwigEngine($container['twig'], $container['view.finder']);
            });
            
            return $resolver;
        });
    }
    
    /**
     * Register the view factory. The factory is available in all views.
     */
    public function registerFactory()
    {
        // Register the View Finder first.
        $this->app->singleton('view.finder', function ($app) {
            return new ViewFinder(new Filesystem(), [$app['path.views']], ['twig', 'php']);
        });
        
        $this->app->singleton('view', function ($container) {
            $factory = new Factory(
                $container['view.engine.resolver'],
                $container['view.finder'],
                $container['events']
            );
            
            // Set the container.
            $factory->setContainer($container);
            
            // Tell the factory to handle twig extension files and assign them to the twig engine.
            $factory->addExtension('twig', 'twig');
            $factory->share('app', $container);
            
            return $factory;
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerMarkupTags();
        $this->registerTemplateHierarchy();
    }
    
    /**
     * Register Twig Markup tags
     */
    protected function registerMarkupTags()
    {
        $twig = $this->app['twig'];
        $filters = $this->app['config']->get('twig.filters', []);
        $functions = $this->app['config']->get('twig.functions', []);
        
        // register twig filters
        foreach ($filters as $name => $filter) {
            $twig->addFilter(new \Twig_SimpleFilter($name, $filter));
        }
        
        // register twig functions
        foreach ($functions as $name => $func) {
            $twig->addFunction(new \Twig_SimpleFunction($name, $func));
        }
    }
    
    /**
     * Register Wordpress Template Hierarchy for twig files
     */
    protected function registerTemplateHierarchy()
    {
        // register template finder
        $this->app->singleton('templateHierarchy.finder', function ($app) {
            return new TemplateFinder($app['view']);
        });
        
        $this->app->singleton('templateHierarchy', function ($app) {
            return new TemplateHierarchy($app['view'], $app['templateHierarchy.finder']);
        });
    }
}
