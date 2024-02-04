<?php

require_once __DIR__ . '/../bin/functions.php';


define('OUTPUT_FORMAT', 'json');
define('OUTPUT_DIR', 'output/');

define( 'REMOTE_SERVER_DOMAIN', getenv_docker('ENV_REMOTE_SERVER_DOMAIN', 'localhost') );
define( 'REMOTE_SERVER_PROTOCOL', getenv_docker('ENV_REMOTE_SERVER_PROTOCOL', 'http') );

define( 'REMOTE_KEY_FULL_RESPONSE', getenv_docker('ENV_REMOTE_KEY_FULL_RESPONSE', 'DEADBEEFDEADBEEFDEADBEEFDEADBEEF') );
