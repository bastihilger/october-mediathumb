<?php namespace manogi\Mediathumb;

use Backend;
use System\Classes\PluginBase;
use Config;

/**
 * Mediathumb Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */

    

    public function pluginDetails()
    {
        return [
            'name'        => 'Mediathumb',
            'description' => 'Twig filter for automatic thumbnail images for your media images.',
            'author'      => 'manogi',
            'icon'        => 'icon-compress'
        ];


    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'mediathumb_resize' => [$this, 'mediathumb_resize']
            ]
        ];
    }

    public function mediathumb_resize($img, $mode=null, $size=null, $quality=null, $except=null)
    {   
        return mediathumbGetThumb($img, $mode, $size, $quality, $except);
    }


}
