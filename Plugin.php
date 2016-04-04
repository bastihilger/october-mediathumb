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

    public function mediathumb_resize($img, $mode='auto', $size=200, $quality=90)
    {   

        // Add slash at the beginning if omitted
        if(substr($img, 0, 1) != '/'){
            $img = '/'.$img;
        }

        // define complete path of original for Storage (without the root path)
        $original_path = 'media'.$img;

        // return empty String if file does not exist
        if(!Storage::exists($original_path)){
            return '';
        }
        
        // define complete path of original for Intervention and vanilla PHP (including the root path)
        $root_original_path = storage_path().'/app/'.$original_path;

        // check if image is a PNG, JPG or Gif, otherwise return empty String
        $allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
        $detected_type = exif_imagetype($root_original_path);
        if(!in_array($detected_type, $allowed_types)){
            return '';
        }

        // define directory for thumbnail
        $thumb_directory = 'mediathumbs/';

        // make new filename for folder names and filename
        $new_filename = str_replace('/', '-', substr($img, 1));

        // store position of the dot before the extension
        $last_dot_position = strrpos($new_filename, '.');

        //get the extension
        $extension = substr($new_filename, $last_dot_position+1);

        //get the new filename without extension
        $filename_body = substr($new_filename, 0, $last_dot_position);

        // get filesize and filetime for extending the filename for the purpose of
        // creating a new thumb in case a new file with the same name is uploaded
        // (meaning the orginal file is overwritten)
        $filesize = Storage::size($original_path);
        $filetime = Storage::lastModified($original_path);

        // make the string to add to the filname to for 2 purposes:
        // A) to make sure the that for the SAME image a thumbnail is only generated once
        // b) to make sure that a new thumb is generated if the original is overwritten
        $version_string = $mode.'-'.$size.'-'.$quality.'-'.$filesize.'-'.$filetime;

        // create the complete new filename and hash the version string to make it shorter
        $new_filename = $filename_body.'-'.md5($version_string).'.'.$extension;

        // define complete path of the new file (without the root path)
        $new_path = $thumb_directory.$new_filename;

        // define complete path of the new file (including the root path)
        $root_new_path = storage_path().'/app/'.$new_path;

        // create the thumb directory if it does not exist
        if(!Storage::exists($thumb_directory)){
            Storage::makeDirectory($thumb_directory);
        }
        
        // create the thumb, but only if it does not exist
        if(!Storage::exists($new_path)){
            $image = Image::make($root_original_path);
            
            $final_mode = $mode;
            if($mode == 'auto'){
                $final_mode = 'width';
                $sizes = getimagesize($root_original_path);
                $ratio = $sizes[0]/$sizes[1];
                if($ratio < 1){
                    $final_mode = 'height';
                }
            }
            if($final_mode == 'width'){
                $image->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            elseif($final_mode == 'height'){
                $image->resize(null, $size, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            $image->save($root_new_path, $quality);
           
        }

        return '/storage/app/mediathumbs/'.$new_filename;


    }


}
