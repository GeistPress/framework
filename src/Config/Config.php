<?php
namespace GeistPress\Config;

use Illuminate\Config\Repository;

/**
 * Class Config
 * @package GeistPress\Config
 */
class Config extends Repository
{
    /**
     * @var \GeistPress\Config\ConfigFinder
     */
    protected $finder;
    
    /**
     * Set finder
     * @param \GeistPress\Config\ConfigFinder $finder
     */
    public function setFinder(ConfigFinder $finder)
    {
        $this->finder = $finder;
    }
    
    /**
     * Load the configuration items from all of the files.
     * @param string|null $environment
     */
    public function load($environment = null)
    {
        foreach ($this->finder->getFiles() as $fileKey => $path) {
            $this->set($fileKey, require $path);
        }

        foreach ($this->finder->getFiles($environment) as $fileKey => $path) {
            $envConfig = require $path;

            foreach ($envConfig as $envKey => $value) {
                $this->set($fileKey . '.' . $envKey, $value);
            }
        }
    }
}
