<?php

/*
* Replace file extension
*/

function replace_extension($filename, $new_extension) {
    $info = pathinfo($filename);
    return $info['filename'] . '.' . $new_extension;
}

/*
* Return a microtime (to measure execution time)
*/
function getmicrotime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

/*
* Delete recursively a folder
*/
function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
} 



/*
* Format fileSize
*/
function fileSizeRender($filesize = 0){
  $range = 0;
  $units = array('B', 'KB', 'MB', 'GB', 'TB');
  while($filesize >= 1024 && $range < 4) {
    $range++;
    $filesize /= 1024;
  }
  $filesize = ($filesize < 10) ? round($filesize, 1) : round($filesize);
  return $filesize . '' . $units[$range];
}

/*
* Format string for CLI Interface
*/
function cli_output($str=null){
    echo $str . "\n";
}


/*
* Generate a XML output from array (sub function)
*/
function generate_xml_from_array($array, $node_name) {
    $xml = '';

    if (is_array($array) || is_object($array)) {
        foreach ($array as $key=>$value) {
            if (is_numeric($key)) {
                $key = $node_name;
            }

            $xml .= '<' . $key . '>' . "\n" . generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
        }
    } else {
        $xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
    }

    return $xml;
}


/*
* Generate a XML output from array (main function)
*/
function generate_valid_xml_from_array($array, $node_block='output', $node_name='node') {
    $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

    $xml .= '<' . $node_block . '>' . "\n";
    $xml .= generate_xml_from_array($array, $node_name);
    $xml .= '</' . $node_block . '>' . "\n";

    return $xml;
}

/*
* Get ENV from Docker env/file
*/
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

/*
* Check CLI mode
*/
function isCLI()
{
    return PHP_SAPI === 'cli' || defined('STDIN');
}