<?php 
error_reporting(E_ALL);
ini_set('display_errors','On');

require_once __DIR__ . '/config/settings_remote.ini.php';
require_once __DIR__ . '/config/webservice.ini.php';
require_once __DIR__ . '/bin/functions.php';

if(!isCLI()) {
    die('cli mode only');
}

cli_output('Start: ' . time());

cli_output('Finding files older than ' . EXPIRED_LIMIT . ' sec...');
$result = array();
$handle =  opendir(OUTPUT_DIR);
while ($datei = readdir($handle)) 
{    
    if (($datei != '.') && ($datei != '..') && ($datei != '.htaccess') && ($datei != 'error.php')) 
    {
        $file = OUTPUT_DIR . $datei;
        if (is_dir($file) AND filemtime ( $file ) < EXPIRED_LIMIT){
            $result[] = $file;
        }
    }
}
closedir($handle);

cli_output(sizeof($result) . ' files found');
if(sizeof($result) > 0){
    cli_output('Deleting files...');
    foreach($result as $r)
        rrmdir($r);

    cli_output('Files deleted');
}

cli_output('Finish: ' . time() . PHP_EOL);
