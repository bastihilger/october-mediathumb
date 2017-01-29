<?php
namespace manogi\Mediathumb;

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
            'name'        => 'manogi.mediathumb::lang.plugin.name',
            'description' => 'manogi.mediathumb::lang.plugin.description',
            'author'      => 'manogi',
            'icon'        => 'icon-compress',
            'homepage'    => 'https://github.com/manogi/october-mediathumb'
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

    public function mediathumb_resize($img, $mode = null, $size = null, $quality = null)
    {
        return mediathumbResize($img, $mode, $size, $quality, 'media');
    }

   
}
