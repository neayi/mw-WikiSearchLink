# mw-Piwigo
This is a mediawiki extension that displays a gallery of images extracted from a Piwigo setup

## What this does

This extension adds a ```<piwigo />``` keyword and a ```{{#piwigo}}``` parser function that show a gallery in a page. The keyword can contain the same kind of parameters as Piwigo's URL (category, tags, ...):

### Tags parameter ###
You can select all photos for a given tag by using: ```{{#piwigo:tags=1-tagname}}``` or ```<piwigo tags="1-tagname"/>```  or ```<piwigo tags="1"/>``` (only the tag id is relevant).

It is also possible to target more than one tag with the parser function: ```{{#piwigo: tags=3 | tags=4 | count=5 }}``` (not that for that you'll need to use the parser function and not the keyword - ie. ```<piwigo  tags=3 | tags=4 | count=5>``` will only show images from tag 4)

If the ```tags``` parameter is set, the ```category``` is ignored.

### Category parameter ###
The category parameter is used to select photos from an album. You cannot select both an album and a tag (both are mutually exclusive): ```{{#piwigo: category = 5}}```

### Count parameter ###
You can use the ```count``` parameter to limit the number of results: ```{{#piwigo: category = 5 | count = 10}}```  or ```<piwigo tags="1" count = 4/>```

## Performance ##
The images are loaded in JS which means that the page is effectively cached as any wiki page, and checks for new images only at display time.

The images are shown using this JS gallery: https://tutorialzine.com/2017/02/freebie-4-bootstrap-galleries (the four layouts are available)

## Configuration

You will need to store the extension in ```extensions/Piwigo```, then add the following to your LocalSettings.php:

```
wfLoadExtension( 'Piwigo' );
$wgPiwigoURL = 'https://somegallery.piwigo.fr';
$wgPiwigoGalleryLayout = 'fluid'; // one of the four: fluid (default), grid, thumbnails, clean
```

## CORS setting on the Piwigo server ##

If mediawiki and Piwigo are not hosted on the same domain, it will be necessary to setup CORS on Piwigo (so that Mediawiki's HTML can ping Piwigo's web services).

In order to do that, you might want to use the following Nginx config file (Piwigo's docker setup):

```
map $http_origin $allow_origin {
    ~^https?://(.*\.)?yourwikidomain.com(:\d+)?$ $http_origin;
    ~^https?://(.*\.)?localhost(:\d+)?$ $http_origin;
    default "";
}

server {
	listen 80 default_server;

	listen 443 ssl;

	root /config/www/gallery;
	index index.html index.htm index.php;

	server_name _;

	ssl_certificate /config/keys/cert.crt;
	ssl_certificate_key /config/keys/cert.key;

	client_max_body_size 0;

	# CORS
  add_header 'Access-Control-Allow-Origin' $allow_origin;
	add_header 'Access-Control-Allow-Methods' 'GET';

	location / {
		try_files $uri $uri/ /index.html /index.php?$args =404;
	}

	location ~ \.php$ {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		# With php5-cgi alone:
		fastcgi_pass 127.0.0.1:9000;
		# With php5-fpm:
		#fastcgi_pass unix:/var/run/php5-fpm.sock;
		fastcgi_index index.php;
		include /etc/nginx/fastcgi_params;

	}
}
```
