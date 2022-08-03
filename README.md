# mw-Piwigo
This is a mediawiki extension that displays a gallery of images extracted from a Piwigo setup

## What this does

This extension adds a ```{{#piwigo}}``` keyword that shows a gallery in a page. The keyword can contain the same kind of parameters as Piwigo's URL (category, tags, ...). You can insert ```{{#piwigo|tags=1-my-tag}}``` or ```{{#piwigo|tags=1}}``` for short (only the id is taken in account).

The images are loaded in JS which means that the page is effectively cached as any wiki page, and checks for new images only at display time.

The images are shown using this JS gallery: https://tutorialzine.com/2017/02/freebie-4-bootstrap-galleries (the four layouts are available)

## Configuration

You will need to store the extension in ```extensions/Piwigo```, then add the following to your LocalSettings.php:

```
wfLoadExtension( 'Piwigo' );
$wgPiwigoURL = 'https://somegallery.piwigo.fr';
$wgPiwigoGalleryLayour = 'fluid'; // one of the four: fluid (default), grid, thumbnails, clean
```
