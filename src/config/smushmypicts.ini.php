<?php

if(isset($_GET['token']) AND $_GET['token'] != '')
    define('TOKEN', $_GET['token']);
else
    define('TOKEN', md5(time() . rand()));

if(isset($_GET['baseurl']))
    define('SCAN_URL', $_GET['baseurl']);

define('LIMIT', 9999);
define('WEBSERVICE', WEBSERVICE_URL . '?img=');
define('TMP_DIR', 'tmp/');
