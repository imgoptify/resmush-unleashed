<?php

require_once __DIR__ . '/config/webservice.ini.php';

$_output = new stdClass;
$_output->error = 404;
$_output->error_long = 'Unknown method or resource requested.';
$_output->generator = APPNAME . ' rev.' . APPVER;

http_response_code(404);
header('Content-type: application/json');
echo json_encode(get_object_vars($_output));


