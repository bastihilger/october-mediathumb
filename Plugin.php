<?php namespace Manogi\Mediathumb;

use Backend;
use System\Classes\PluginBase;
use App;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;


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
            'author'      => 'Manogi',
            'icon'        => 'icon-compress'
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'mediathumb' => [$this, 'mediathumb']
            ]
        ];
    }

    public function mediathumb($img, $mode, $width, $height, $quality=90, $aspect_ratio=1, $horizontal='center', $vertical ='center')
    {   
        $original_path = 'media'.$img;
        $root_original_path = storage_path().'/app/'.$original_path;
        
        $thumb_directory = 'mediathumbs/';
        $new_filename = str_replace('/', '-', substr($img, 1));
        $last_dot_position = strrpos($new_filename, '.');
        $filesize = Storage::size($original_path);
        $filetime = Storage::lastModified($original_path);
        $extension = substr($new_filename, $last_dot_position+1);
        $filename_body = substr($new_filename, 0, $last_dot_position);
        $version_string = $mode.'-'.$width.'-'.$height.'-'.$horizontal.'-'.$aspect_ratio.'-'.$quality.'-'.$vertical.'-'.$filesize.'-'.$filetime;

        $new_filename = $filename_body.'-'.md5($version_string).'.'.$extension;
        $new_path = $thumb_directory.$new_filename;
        $root_new_path = storage_path().'/app/'.$new_path;
        if(!Storage::exists($thumb_directory)){
            Storage::makeDirectory($thumb_directory);
        }
        if(!Storage::exists($new_path)){
            //$file = Storage::get($original_path);
            //Storage::copy($original_path, $new_path);
            //Image::make($original_path)->resize($width, $height)->save($new_path);

            $image = Image::make($root_original_path);
            if($mode == 'resize'){
                
                if($height == 'auto' && is_integer($width)){
                    $image->resize($width, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                if($width == 'auto' && is_integer($height)){
                    $image->resize(null, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                $image->save($root_new_path, $quality);
            }
        }

        return '/storage/app/mediathumbs/'.$new_filename;


    }


}
