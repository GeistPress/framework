<?php
namespace GeistPress\Config;

use Symfony\Component\Finder\Finder;

class ConfigFinder
{
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    /**
     * Get the configuration files for the selected environment
     * @param string|null $environment
     * @return array
     */
    public function getFiles($environment = null)
    {
        $path = $this->path;
        
        if ($environment) {
            $path .= '/' . $environment;
        }
        
        if (!is_dir($path)) {
            return [];
        }
        
        $files = [];
        $phpFiles = Finder::create()->files()->name('*.php')->in($path)->depth(0);
        
        foreach ($phpFiles as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        
        return $files;
    }
}