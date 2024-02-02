<?php

/*
* Create a statistic entry based on ws response
*/

/*
* Return the loadavg mean between 1m, 5m and 15m
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
  while($filesize > 1024):
    $range++;
    $filesize = $filesize / 1024;
  endwhile;
  if($filesize < 10):
    $filesize = round($filesize, 1);
  else: 
    $filesize = round($filesize, 0);
  endif;
  
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
