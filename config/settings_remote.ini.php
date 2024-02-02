<?php
date_default_timezone_set('Europe/Moscow');

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

define( 'OUTPUT_FORMAT', getenv_docker('RESMUSH_OUTPUT_FORMAT', 'json') );
define( 'OUTPUT_DIR', getenv_docker('RESMUSH_OUTPUT_DIR', 'output/') );
define( 'LOADAVG_ALERT_THRESHOLD', getenv_docker('RESMUSH_LOADAVG_ALERT_THRESHOLD', 20) );
define( 'REMOTE_SERVER', getenv_docker('RESMUSH_REMOTE_SERVER', 'http://localhost/') );
define( 'REMOTE_SERVER_ID', getenv_docker('RESMUSH_REMOTE_SERVER_ID', 'localhost') );
define( 'REMOTE_SERVER_FULL_URL', getenv_docker('RESMUSH_REMOTE_SERVER_FULL_URL', 'http://localhost/') );
define( 'REMOTE_KEY_FULL_RESPONSE', getenv_docker('RESMUSH_REMOTE_KEY_FULL_RESPONSE', 'D3732B6610CD7EB530D6B2BDBB13F05E') );
//$AUTHORIZED_IP = array();
