<?php 
error_reporting(E_ALL);
ini_set('display_errors','On');
require_once __DIR__ . '/config/settings_remote.ini.php';
require_once __DIR__ . '/config/webservice.ini.php';
require_once __DIR__ . '/bin/functions.php';

$_output = new stdClass;
$_app = new stdClass;
$_server = new stdClass;
$_service = new stdClass;

$OUTPUT_FORMAT_override = OUTPUT_FORMAT;
$_app->version = APPVER;
$_app->identifier = REMOTE_SERVER_DOMAIN;

$sections = ['_app', '_server', '_service'];
if(isset($_GET['section']) && is_array($_GET['section'])) {
    $sections=$_GET['section'];
}

//Check disk space
$df = disk_free_space("/var");
$dt = disk_total_space("/var");
$du = $dt - $df;
$dp = sprintf('%.2f',($du / $dt) * 100);
if($dp > DISK_USAGE_THRESHOLD){
    $_error = new stdClass;
    $_error->code = 'disk_full';
    $_error->long = 'Disk space beyond ' . DISK_USAGE_THRESHOLD . '%';
    $_error->more = $dp . '%';
    $_app->error[] = $_error;
}

$ramfs_df = disk_free_space(__DIR__ . '/' . OUTPUT_DIR);
$ramfs_dt = disk_total_space(__DIR__ . '/' . OUTPUT_DIR);
$ramfs_du = $ramfs_dt - $ramfs_df;
$ramfs_dp = sprintf('%.2f',($ramfs_du / $ramfs_dt) * 100);
if($ramfs_dp > DISK_USAGE_THRESHOLD){
    $_error = new stdClass;
    $_error->code = 'disk_full';
    $_error->long = 'RAMFS space beyond ' . DISK_USAGE_THRESHOLD . '%';
    $_error->more = $ramfs_dp . '%';
    $_app->error[] = $_error;
}

$_server->hostname = gethostname();
$_server->disk_free = fileSizeRender($df);
$_server->disk_total = fileSizeRender($dt);
$_server->disk_use = $dp;
$_server->ramfs_free = fileSizeRender($ramfs_df);
$_server->ramfs_total = fileSizeRender($ramfs_dt);
$_server->ramfs_use = $ramfs_dp;
$_server->phpversion = phpversion();

/*Curl Test*/
if(in_array('_service', $sections)){
    $ch = curl_init();
    $localWebservice = REMOTE_SERVER_PROTOCOL . '://' . REMOTE_SERVER_DOMAIN . '/ws.php?img=https://resmush.it/assets/images/compare_1_original.jpg&qlty=30&key=' . REMOTE_KEY_FULL_RESPONSE;
    curl_setopt($ch, CURLOPT_URL, $localWebservice);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_USERAGENT, "ReSmush.it RemoteStatusAgent " . APPVER );

    $data = curl_exec($ch);
    curl_close($ch);
    if($data){
        $json = json_decode($data);
        if(isset($json->error)){
            $_service->code = (int)$json->error;
            $_service->error = $json->error_long;
        } else {
            $_service->code = 200;
            $_service->gentime = $json->gentime;
            $_service->percentGainTest = $json->percent;
        }
    } else {
        $_service->code = 500;
        $_service->error = "Local service unreachable";
    }
    $_output->_service = $_service;
}

if(in_array('_app', $sections)){
    $_output->_app = $_app;
} 
if(in_array('_server', $sections)){
    $_output->_server = $_server;
}

if($OUTPUT_FORMAT_override == 'json'){
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    echo json_encode(get_object_vars($_output));
}
