<?php
namespace GeistPress\Config;

use Illuminate\Support\ServiceProvider;

/**
 * Class ConfigServiceProvider
 * @package GeistPress\Config
 */
class ConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('config.finder', function ($app) {
            return new ConfigFinder($app['path.config']);
        });
    
        $this->app->singleton('config', function ($app) {
            $config = new Config();
            $config->setFinder($app['config.finder']);
            return $config;
        });
    }
}
