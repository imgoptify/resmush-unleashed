<?php

if (!function_exists('getenv_docker')) {
    function getenv_docker($env, $default) {
        if ($fileEnv = getenv($env . '_FILE')) {
            return rtrim(file_get_contents($fileEnv), "\r\n");
        }
        else if (($val = getenv($env)) !== false) {
            return $val;
        }
        else {
            return $default;
        }
    }
}

define( 'TOKEN', getenv_docker('RESMUSH_TOKEN', md5(time() . rand())) );
define( 'APPNAME', getenv_docker('RESMUSH_APPNAME', 'unleashed reSmush') );
define( 'APPVER', getenv_docker('RESMUSH_APPVER', '3.0.4.20210124') );
define( 'OPTIPNG_PATH', getenv_docker('RESMUSH_OPTIPNG_PATH', '/usr/bin/optipng -o 3') );
define( 'PNGQUANT_PATH', getenv_docker('RESMUSH_PNGQUANT_PATH', '/usr/bin/pngquant --force') );
define( 'JPEGOPTIM_PATH', getenv_docker('RESMUSH_JPEGOPTIM_PATH', '/usr/bin/jpegoptim --max=') );
define( 'WEBP_PATH', getenv_docker('RESMUSH_WEBP_PATH', '/usr/bin/cwebp -m 6 -mt -q ') );
define( 'AVIF_PATH', getenv_docker('RESMUSH_AVIF_PATH', '/usr/bin/avif -s 8') );
define( 'JPEGOPTIM_LEVEL', getenv_docker('RESMUSH_JPEGOPTIM_LEVEL', '92') );
define( 'WEBPMINORFACTOR_LEVEL', getenv_docker('RESMUSH_WEBPMINORFACTOR_LEVEL', '0.85') );
define( 'EXPIRES', getenv_docker('RESMUSH_EXPIRES', time() + 300) );
define( 'EXPIRED_LIMIT', getenv_docker('RESMUSH_EXPIRED_LIMIT', time() - 300) );
define( 'MAX_FILESIZE', getenv_docker('RESMUSH_MAX_FILESIZE', 5242880 * 5) ); //25Mb
define( 'DISK_USAGE_THRESHOLD', getenv_docker('RESMUSH_DISK_USAGE_THRESHOLD', 95) );

$_AUTHORIZED_EXTENSIONS = array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'tif', 'bmp', 'webp');
