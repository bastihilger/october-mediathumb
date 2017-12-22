<?php

use Intervention\Image\ImageManagerStatic as Image;

// Get the thumb.
if (!function_exists('mediathumbResize')) {
    function mediathumbResize($img, $mode = null, $size = null, $quality = null)
    {
        // return empty String if $img is falsy
        if (!$img) {
            return '';
        }

        // remove app.url if given
        if (starts_with($img, config('app.url'))) {
            $img = str_replace(config('app.url'), '', $img);
        }

        // Add slash at the beginning if omitted
        if (substr($img, 0, 1) != '/'
            && substr($img, 0, 4) != 'http') {
            $img = '/'.$img;
        }


        // check $img string to see if resource is actually "uploads", not "media"


        $resource = 'media';
        $uploads_path = config('cms.storage.uploads.path');

        if (substr($img, 0, strlen($uploads_path)) == $uploads_path) {
            $resource = 'uploads';
        }


        if (!$mode) {
            $mode = config('manogi.mediathumb::default.mode');
        }
        if (!$size) {
            $size = config('manogi.mediathumb::default.size');
        }
        if (!$quality) {
            $quality = config('manogi.mediathumb::default.quality');
        }

        // get folder inside media directory from config

        $mediathumb_folder = config('manogi.mediathumb::folder');



        $disk = config('cms.storage.'.$resource.'.disk');
        $disk_folder = config('cms.storage.'.$resource.'.folder');

        $original_path = $disk_folder.$img;

        // remove absolute path oarts from uploads specific url
        if ($resource == 'uploads') {
            $original_path = str_replace(
                config('cms.storage.'.$resource.'.path'),
                '',
                $img
            );

            $original_path = $disk_folder.$original_path;
        }

        // return empty String if file does not exist
        if (!Storage::disk($disk)->exists($original_path)) {
            return '';
        }



        // get the image as data
        $original_file = Storage::disk($disk)->get($original_path);



        // define directory for thumbnail
        $thumb_directory = $disk_folder.'/'.$mediathumb_folder.'/';


        // make new filename for folder names and filename
        $new_filename = str_replace('/', '-', substr($img, 1));

        // store position of the dot before the extension
        $last_dot_position = strrpos($new_filename, '.');

        // get the extension
        $extension = substr($new_filename, $last_dot_position+1);

        // get the new filename without extension
        $filename_body = str_slug(substr($new_filename, 0, $last_dot_position));



        // get filesize and filetime for extending the filename for the purpose of
        // creating a new thumb in case a new file with the same name is uploadsed
        // (meaning the orginal file is overwritten)
        $filesize = Storage::disk($disk)->size($original_path);
        $filetime = Storage::disk($disk)->lastModified($original_path);

        // make the string to add to the filename to for 2 purposes:
        // a) to make sure the that for the SAME image a thumbnail is only generated once
        // b) to make sure that a new thumb is generated if the original is overwritten
        $version_string = $mode.'-'.$size.'-'.$quality.'-'.$filesize.'-'.$filetime;

        // create the complete new filename and hash the version string to make it shorter
        $new_filename = $filename_body.'-'.md5($version_string).'.'.$extension;

        // define complete path of the new file (without the root path)
        $new_path = $thumb_directory.$new_filename;


        // create the thumb directory if it does not exist
        if (!Storage::disk($disk)->exists($thumb_directory)) {
            Storage::disk($disk)->makeDirectory($thumb_directory);
        }

        // create the thumb, but only if it does not exist
        if (!Storage::disk($disk)->exists($new_path)) {
            if ($extension == 'gif') {
                Storage::disk($disk)->put($new_path, $original_file);
            } else {
                try {
                    $image = Image::make($original_file);
                    $final_mode = $mode;
                    if ($mode == 'auto') {
                        $final_mode = 'width';

                        $ratio = $image->width()/$image->height();
                        if ($ratio < 1) {
                            $final_mode = 'height';
                        }
                    }
                    if ($final_mode == 'width') {
                        $image->resize($size, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    } elseif ($final_mode == 'height') {
                        $image->resize(null, $size, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    $image_stream = $image->stream($extension, $quality);
                    Storage::disk($disk)->put($new_path, $image_stream->__toString());
                } catch (Exception $e) {
                    $error = 'Intervention Image Error : '.$e->getMessage();
                }
            }
        }


        // return image path
        return asset(config('cms.storage.'.$resource.'.path').'/'.$mediathumb_folder.'/'.$new_filename);
    }
}


// Alias for mediathumbResize()
if (!function_exists('mediathumbGetThumb')) {
    function mediathumbGetThumb($img, $mode = null, $size = null, $quality = null)
    {
        return mediathumbResize($img, $mode, $size, $quality);
    }
}

// Alias for mediathumbResize()
if (!function_exists('getMediathumb')) {
    function getMediathumb($img, $mode = null, $size = null, $quality = null)
    {
        return mediathumbResize($img, $mode, $size, $quality);
    }
}
