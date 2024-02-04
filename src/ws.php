<?php

require_once __DIR__ . '/config/settings.ini.php';
require_once __DIR__ . '/config/settings_remote.ini.php';
require_once __DIR__ . '/config/webservice.ini.php';
require_once __DIR__ . '/bin/functions.php';


$_time_init = getmicrotime();
$img_src = new stdClass;
$_output = new stdClass;
$_output->host = 'N/A';
$_output->client_type =  '';

$OUTPUT_FORMAT_override = OUTPUT_FORMAT;

$JPEGOPTIM_LEVEL_override = JPEGOPTIM_LEVEL;

if(isset($_GET['qlty']) || isset($_POST['qlty'])){
    if(isset($_GET['qlty']))
        $JPEGOPTIM_LEVEL_override = (int)$_GET['qlty'];
    else if(isset($_POST['qlty']))
        $JPEGOPTIM_LEVEL_override = (int)$_POST['qlty'];

    if($JPEGOPTIM_LEVEL_override > 100 OR  $JPEGOPTIM_LEVEL_override < 0)
        $JPEGOPTIM_LEVEL_override = JPEGOPTIM_LEVEL;
}


// Remove EXIF data
$PRESERVE_EXIF = false;
if(isset($_GET['exif'])){
    if(strtolower(strip_tags($_GET['exif'])) == 'true' OR strip_tags($_GET['exif']) == 1)
        $PRESERVE_EXIF = true;
} else if(isset($_POST['exif']) && $_POST['exif']){
    if(strtolower(strip_tags($_POST['exif'])) == 'true' OR strip_tags($_POST['exif']) == 1)
        $PRESERVE_EXIF = true;
}


// FORCE WEBP
$FORCE_OUTPUT_FORMAT = NULL;
if(isset($_GET['format'])){
    if(strtolower(strip_tags($_GET['format'])) == 'webp' || strtolower(strip_tags($_GET['format'])) == 'avif') {
        $FORCE_OUTPUT_FORMAT = strtolower(strip_tags($_GET['format']));
    } else {
        $_output->error = 406;
        $_output->error_long = 'Unauthorized output format';
    }
}

if(isset($_GET['img']) and $_GET['img'] != ''){
    $img_src->url = str_replace(' ', '%20', $_GET['img']);
    $img_src->method = 'url';
    $img_src->method_desc = 'get';
} else if(isset($_POST['img']) and $_POST['img'] != ''){
    $img_src->url = str_replace(' ', '%20', $_POST['img']);
    $img_src->method = 'url';
    $img_src->method_desc = 'post';
} else if(isset($_FILES['files']) and @sizeof($_FILES['files'] != 0)){
    $img_src->method = 'upload';
    $img_src->method_desc = 'file';
}

if(!isset($img_src->method)){
    $_output->error = 400;
    $_output->error_long = 'No file or url provided';
} else {
    if($img_src->method == 'upload'){
        if($_FILES['files']['size'] > MAX_FILESIZE) {
            $_output->error = 502;
            $_output->error_long = 'Uploaded file must be below 5MB';
        } else {
            $img_src->filename = basename($_FILES['files']['name']);
            $_pathinfo = pathinfo($_FILES['files']['name']);

            if(!isset($_pathinfo['extension']) OR !in_array(strtolower($_pathinfo['extension']), $_AUTHORIZED_EXTENSIONS)){
                $_output->error = 403;
                $_output->error_long = 'Unauthorized extension. Allowed are : JPG, PNG, GIF, BMP, TIFF, WEBP';
            }

            if(!isset($_output->error)){
                $img_src->_src_filename = '_src_' . $img_src->filename;
                $img_src->_src_fullpath = OUTPUT_DIR . TOKEN . '/' . $img_src->_src_filename;
                $img_src->_dest_fullpath = OUTPUT_DIR . TOKEN . '/' . $img_src->filename;
                $img_src->_src_url = REMOTE_SERVER_PROTOCOL . '://' . REMOTE_SERVER_DOMAIN . '/' . OUTPUT_DIR . TOKEN . '/' . rawurlencode($img_src->_src_filename);
                $img_src->_dest_url = REMOTE_SERVER_PROTOCOL . '://' . REMOTE_SERVER_DOMAIN . '/' . OUTPUT_DIR . TOKEN . '/' . rawurlencode($img_src->filename);
                @mkdir( OUTPUT_DIR . TOKEN . '/', 0777, true );
                if(strlen($_FILES['files']['name']) > 255) {
                    $_output->error = 405;
                    $_output->error_long = 'File name too long, must be below 255 characters';
                } else if (!move_uploaded_file($_FILES['files']['tmp_name'], $img_src->_src_fullpath)) {
                    $_output->error = 402;
                    $_output->error_long = 'Cannot copy from uploaded file';
                }
                if(!isset($_output->error)){
                    if(!@copy($img_src->_src_fullpath, $img_src->_dest_fullpath))
                    {
                        $_output->error = 501;
                        $_output->error_long = 'Interal Error: Cannot create a local copy';
                    }
                }
            }
        }

        if(!isset($_output->error)){
            $_output->src = $img_src->_src_url;
            $img_src->url = $_output->src;
        }
    }


    if($img_src->method == 'url'){
        //Use remote WS
        $_pathinfo = pathinfo($img_src->url);
        if(!isset($_pathinfo['extension']) OR !in_array(strtolower($_pathinfo['extension']), $_AUTHORIZED_EXTENSIONS)){
            $_output->error = 403;
            $_output->error_long = 'Unauthorized extension. Allowed are : JPG, PNG, GIF, BMP, TIFF, WEBP';
        }

        if(!isset($_output->error)){
            $_output->src = $img_src->url;
            $_output->host = parse_url($img_src->url, PHP_URL_HOST);
            $img_src->filename = urldecode(basename($img_src->url));
            $img_src->_src_filename = '_src_' . $img_src->filename;
            $img_src->_src_fullpath = OUTPUT_DIR . TOKEN . '/' . $img_src->_src_filename;
            $img_src->_dest_fullpath = OUTPUT_DIR . TOKEN . '/' . $img_src->filename;
            $img_src->_dest_url = REMOTE_SERVER_PROTOCOL . '://' . REMOTE_SERVER_DOMAIN . '/' . OUTPUT_DIR . TOKEN . '/' . rawurlencode($img_src->filename);
            $_output->filename = $img_src->filename;
            $_output->token = TOKEN;
            @mkdir( OUTPUT_DIR . TOKEN . '/', 0777, true );

            $arrContextOptions=array(
                                        "ssl" => array(
                                            "verify_peer"       => false,
                                            "verify_peer_name"  => false,
                                        ),
                                    );
            if(!@copy($img_src->url, $img_src->_src_fullpath, stream_context_create($arrContextOptions)))
            {
                $_output->error = 401;
                $_output->error_long = 'Cannot copy from remote url';
            } 
            if(!isset($_output->error)){
                if(!@copy($img_src->_src_fullpath, $img_src->_dest_fullpath))
                {
                    $_output->error = 501;
                    $_output->error_long = 'Interal Error: Cannot create a local copy';
                }
            }
        }
    }


    if(!isset($_output->error)){
        if(file_exists($img_src->_src_fullpath)){
            $img_src->extension = strtolower(pathinfo($img_src->_src_fullpath, PATHINFO_EXTENSION));
            $img_src->size = filesize($img_src->_src_fullpath);
            if($img_src->size > MAX_FILESIZE) {
                $_output->error = 502;
                $_output->error_long = 'Uploaded file must be below 25MB';
            } else {
                if($FORCE_OUTPUT_FORMAT) {
                    $img_src->filename = replace_extension($img_src->filename, $FORCE_OUTPUT_FORMAT);
                    $_output->filename = $img_src->filename;
                    $img_src->_dest_fullpath = OUTPUT_DIR . TOKEN . '/' . $img_src->filename;
                    $img_src->_dest_url = REMOTE_SERVER_PROTOCOL . '://' . REMOTE_SERVER_DOMAIN . '/' . OUTPUT_DIR . TOKEN . '/' . rawurlencode($img_src->filename);
                    switch($FORCE_OUTPUT_FORMAT) {
                        case 'webp':
                            shell_exec(WEBP_PATH . (int)($JPEGOPTIM_LEVEL_override*WEBPMINORFACTOR_LEVEL) . " '$img_src->_src_fullpath' -o '$img_src->_dest_fullpath'");
                            break;
                        case 'avif':
                            shell_exec(AVIF_PATH . " -e '$img_src->_src_fullpath' -o '$img_src->_dest_fullpath'");
                            break;
                    }
                    $img_src->optimized_size = (int)filesize($img_src->_dest_fullpath);
                    $_output->force_format = $FORCE_OUTPUT_FORMAT;
                } else {
                    $_output->force_format = '';
                    switch ($img_src->extension) {
                        case 'webp':
                            shell_exec(WEBP_PATH . (int)($JPEGOPTIM_LEVEL_override*WEBPMINORFACTOR_LEVEL) . " '$img_src->_src_fullpath' -o '$img_src->_dest_fullpath'");
                            $img_src->optimized_size = filesize($img_src->_dest_fullpath);
                            //if compression wasn't efficient, we give back original file
                            if($img_src->optimized_size > $img_src->size){
                                @copy($img_src->_src_fullpath, $img_src->_dest_fullpath);
                                $img_src->optimized_size = $img_src->size; 
                            }
                            break;
                        case 'jpeg':
                        case 'jpg':
                            $extra_parameters = null;
                            if($img_src->size > 10240)
                                $extra_parameters .= " --all-progressive";
                            if($PRESERVE_EXIF == false)
                                $extra_parameters .= " --strip-all";

                            shell_exec(JPEGOPTIM_PATH . $JPEGOPTIM_LEVEL_override . " '$img_src->_dest_fullpath'" . $extra_parameters);
                            $img_src->optimized_size = filesize($img_src->_dest_fullpath);
                            $img_src->extension = 'jpg';
                            break;
                        case 'gif':
                            shell_exec(OPTIPNG_PATH . " '$img_src->_dest_fullpath'");

                            //if GIF is not animated, it's transformed into PNG file
                            $giffile = $img_src->_dest_fullpath;
                            $pngfile = str_replace('.gif', '.png', $img_src->_dest_fullpath);
                            if(file_exists($pngfile)){
                                $img_src->_dest_fullpath = $pngfile;
                            }
                            $img_src->optimized_size = filesize($img_src->_dest_fullpath);

                            //if compression wasn't efficient, we give back original file
                            if($img_src->optimized_size > $img_src->size){
                                $img_src->_dest_fullpath = $giffile;
                                @copy($img_src->_src_fullpath, $img_src->_dest_fullpath);
                                $img_src->optimized_size = $img_src->size; 
                            }
                            break;
                        case 'bmp':
                        case 'tiff':
                            shell_exec(OPTIPNG_PATH . " '$img_src->_dest_fullpath'");
                            $img_src->optimized_size = filesize($img_src->_dest_fullpath);
                            break;
                        case 'png':
                            shell_exec(PNGQUANT_PATH . " $img_src->_src_fullpath --output $img_src->_dest_fullpath");
                            $img_src->optimized_size = filesize($img_src->_dest_fullpath);
                            if($img_src->optimized_size > $img_src->size){
                                @copy($img_src->_src_fullpath, $img_src->_dest_fullpath);
                                $img_src->optimized_size = $img_src->size; 
                            }
                            break;
                        default:
                            $_output->error = 403;
                            $_output->error_long = 'Unauthorized extension. Allowed are : JPG, PNG, GIF, BMP, TIFF';
                            break;
                    }
                }
                if(!isset($_output->error)){
                    $_output->dest = $img_src->_dest_url;
                    $_output->src_size = $img_src->size;
                    $_output->dest_size = $img_src->optimized_size;
                    if($img_src->size == 0)
                        $_output->percent = 0;
                    else
                        $_output->percent = round(100*($img_src->size - $img_src->optimized_size)/$img_src->size);
                    $_output->format = $img_src->extension;
                    $_output->gentime = round(1000*(getmicrotime() - $_time_init), 1);
                    $_output->output = $OUTPUT_FORMAT_override;
                    $_output->method = $img_src->method_desc;
                    $_output->expires = date('r', EXPIRES);
                }
            }   
        }
    }
}

$_output->generator = APPNAME . ' rev.' . APPVER;
$_output->remote_server = REMOTE_SERVER_DOMAIN;

if(!isset($_GET['key']) || $_GET['key'] != REMOTE_KEY_FULL_RESPONSE) {
    if(isset($_output->host) || $_output->host === NULL) {
        unset($_output->host);
    }
    if(isset($_output->format)) {
        unset($_output->format);
    }

    if(isset($_output->filename)) {
        unset($_output->filename);
    }
    if(isset($_output->token)) {
        unset($_output->token);
    }
    if(isset($_output->gentime)) {
        unset($_output->gentime);
    }
    if(isset($_output->method)) {
        unset($_output->method);
    }
    if(isset($_output->remote_server)) {
        unset($_output->remote_server);
    }
    if(isset($_output->force_format)) {
        unset($_output->force_format);
    }
    if(isset($_output->client_type)) {
        unset($_output->client_type);
    }
}
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode(get_object_vars($_output));
