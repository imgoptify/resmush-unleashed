<?php 


/*
* Function to reconstruct URL
*/
function addhttp($url, $base) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = $base . $url;
    }
    return $url;
}


/*
* Format Output
*/
function output($input){
    if(OUTPUT_FORMAT == 'json')
        echo json_encode($input);
    elseif(OUTPUT_FORMAT == 'html') {
        echo '<pre>'; 
        var_dump($input);
    } elseif(OUTPUT_FORMAT == 'xml') {
        header('Content-Type: application/xml; charset=utf-8');
        echo array2xml($input);
    }
}


/*
* Format file size
*/
function bytesToSize1024($bytes, $precision = 2)
{
    $unit = array('B','KB','MB','GB','TB','PB','EB');

    return @round(
        $bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision
    ).' '.$unit[$i];
} 