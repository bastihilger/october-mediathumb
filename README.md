# october-mediathumb

[Here you can find this plugin in the October CMS PLugins page](http://octobercms.com/plugin/manogi-mediathumb).

+ Twig filter for automatic thumbnail images for your media images.
+ Static PHP helper function for automatic thumbnail images for your media images in your backend files.

After installing the plugin you can use it...

...as a Twig filter in other plugins or in your theme files:

    <img src="{{ 'path/to/image.jpg'|mediathumb_resize(mode, size, quality) }}">

... as a static PHP helper function in your backend PHP and .htm files:

    <img src="<?= getMediathumb('path/to/image.jpg', mode, size, quality) ?>">

The filter supports three arguments:

+ _mode_: can bei either 'auto', 'width' or 'height'. 'auto' is the default.
+ _size_: an integer describing the length in pixels (defaults to 200) of
    - the longer edge of the image in 'auto' mode
    - the width in 'width' mode
    - the height in 'height' mode
+ _quality_: an integer from 1 – 100 to set the quality of the image. Only applies to JPGs. Defaults to 90.

The static PHP helper function needs the image path as a string as the first argument. 

## Examples:

### Twig (frontend code)

    <img src="{{ 'path/to/image.jpg'|mediathumb_resize() }}">

Creates and displays a 200px wide thumbnail image of an landscape image or a 200px high thumbnail image of a portrait image. 


    <img src="{{ 'path/to/image.jpg'|mediathumb_resize('height', 400) }}">

Creates and displays a 400px high thumbnail image, no matter if the original is a landscape or a portrait image. 


    <img src="{{ 'path/to/image.jpg'|mediathumb_resize('width', 800, 96) }}">

Creates and displays a 800px wide thumbnail image with a quality of 96, no matter if the original is a landscape or a portrait image. 


### Static PHP helper function (backend code)

The static PHP helper function needs the image path as a string as the first argument. You can use it for example when you display a list of items in the backend, using the default `$record` variable you get when using the default October CMS `$this->listRender()` function:

    <img src="<?= getMediathumb($record->image, 'height', 180, 96) ?>">

Creates and displays a 180px high thumbnail image, no matter if the original is a landscape or a portrait image.

While of course `$record->image` might be something else in your case. "image" is here the name of the field you store your image in.

You can of course also use the defaults like so:

    <img src="<?= getMediathumb($record->image) ?>">

Creates and displays a 200px wide thumbnail image of an landscape image or a 200px high thumbnail image of a portrait image. 



## How does it work:

The plugin checks if a thumbnail for the original image was already created - if not, it creates the thumbnail.  
Then the thumbnail path is returned.

## What if I overwrite the original with an altered version?

The plugin uses the filetime and filesize in naming the thumbnail to make sure that altered images with the same name don't produce old thumbnails.

## Where are the thumbnails stored?

In a "_mediathumbs" (which is created automatically) folder in your storage media folder.

## Does it work with Amazon S3?

Yep.

## What happens to the thumbnail files once I delete the original?

So far they just stay in the "_mediathumbs" folder. I am working on a solution to have them deleted together with the originals, but remember you can easily empty or delete the mediathumbs folder altogether - the thumbnails will just start being re-created when people hit your website.

## Roadmap

+ Adding a `mediathumb_square` filter for creating automatic square thumbs.

+ ... (let me know if you have feature requests. No promises, though...)



