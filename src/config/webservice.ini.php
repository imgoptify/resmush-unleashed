<?php

require_once __DIR__ . '/../bin/functions.php';


define('APPNAME', 'unleashed reSmush.it');
define('APPVER', '3.0.4.20210124');

define('TOKEN', md5(time() . rand()));
define('EXPIRES', time() + 300);
define('EXPIRED_LIMIT', time() - 300);

define('OPTIPNG_PATH', '/usr/bin/optipng -o 3');
define('PNGQUANT_PATH', '/usr/bin/pngquant --force');
define('JPEGOPTIM_PATH', '/usr/bin/jpegoptim --max=');
define('WEBP_PATH', '/usr/bin/cwebp -m 6 -mt -q ');
define('AVIF_PATH', '/usr/bin/avif -s 8');

define( 'JPEGOPTIM_LEVEL', getenv_docker('ENV_JPEGOPTIM_LEVEL', 92) );
define( 'WEBPMINORFACTOR_LEVEL', getenv_docker('ENV_WEBPMINORFACTOR_LEVEL', 0.85) );

define( 'DISK_USAGE_THRESHOLD', getenv_docker('ENV_DISK_USAGE_THRESHOLD', 95) );
define( 'MAX_FILESIZE', getenv_docker('ENV_MAX_FILESIZE', 26214400) ); // 25 Mb

$_AUTHORIZED_EXTENSIONS = array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'tif', 'bmp', 'webp');
