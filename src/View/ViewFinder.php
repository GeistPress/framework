<?php
namespace GeistPress\View;

use Illuminate\View\FileViewFinder;

/**
 * Class ViewFinder
 * @package GeistPress\View
 */
class ViewFinder extends FileViewFinder
{
    /**
     * Return a list of found views.
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }
}
