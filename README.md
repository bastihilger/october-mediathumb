# october-mediathumb
Twig filter for automatic thumbnail images for your media images.

After installing the plugin you can use the new filter like this in other plugins or in your theme files:

    <img src="{{ 'path/to/image.jpg'|mediathumb_resize(mode, size, quality) }}">

The filter supports three arguments:

+ _mode_: can bei either 'auto', 'width' or 'height'. 'auto' is the default.
+ _size_: an integer describing the length in pixels (defaults to 200) of
    - the longer edge of the image in 'auto' mode
    - the width in 'width' mode
    - the height in 'height' mode
+ _quality_: an integer from 1 – 100 to set the quality of the image. Only applies to JPGs. Defaults to 90.

## Examples:

    <img src="{{ 'path/to/image.jpg'|mediathumb_resize() }}">

Creates and displays a 200px wide thumbnail image of an landscape image or a 200px high thumbnail image of a portrait image. 


    <img src="{{ 'path/to/image.jpg'|mediathumb_resize('height', 400) }}">

Creates and displays a 400px high thumbnail image, no matter if the original is a landscape or a portrait image. 


    <img src="{{ 'path/to/image.jpg'|mediathumb_resize('width', 800, 96) }}">

Creates and displays a 800px wide thumbnail image with a quality of 96, no matter if the original is a landscape or a portrait image. 

## How does it work:

The plugin checks if a thumbnail for the original image was already created - if not, it creates the thumbnail.  
Then the thumbnail path is returned.

## What if I overwrite the original with a altered version?

The plugin uses the filetime and filesize in naming the thumbnail to make sure that altered images with the same name don't produce old thumbnails.

## Where are the thumbnails stored?

In the folder "medialthumbs" in your "storage/app" folder.

## What happens to the thumbnail files once I delete the original?

So far they just stay in the "mediathumbs" folder. I am working on a solution to have them deleted together with the originals, but remember you can easily empty or delete the mediathumbs folder altogether - the thumbnails will just start being re-created when people hit your website.
