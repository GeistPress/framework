<?php
namespace GeistPress\Config;

use Symfony\Component\Finder\Finder;

/**
 * Class ConfigFinder
 * @package GeistPress\Config
 */
class ConfigFinder
{
    /**
     * @var string
     */
    protected $path;
    
    /**
     * Set config root path
     * @param string $path
     */
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
        // check path
        $path = $this->path;
        
        if ($environment) {
            $path .= '/' . $environment;
        }
        
        if (!is_dir($path)) {
            return [];
        }
    
        // get php files
        $files = [];
        $phpFiles = Finder::create()->files()->name('*.php')->in($path)->depth(0);
        
        foreach ($phpFiles as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        
        return $files;
    }
}
