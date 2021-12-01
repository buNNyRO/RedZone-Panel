<?php

if(!isset($_SESSION)) session_start(); 
ob_start();
define('Y_API_KEY', 'CONSTWARE-7CIWDJ5LA-API_CW');

define('PRINC',__DIR__ . '/');
define('SYSTEM',__DIR__ . '/system/');
define('STRUCTURE',__DIR__. '/str/');
define('STYLE',STRUCTURE . '/global/');

include PRINC . 'system/autoload.php';
include_once SYSTEM . 'this.auto.php';
include_once SYSTEM . 'connect.auto.php';
include_once SYSTEM . 'redirect.auto.php';
include_once SYSTEM . 'auth.auto.php';
include_once SYSTEM . 'user.auto.php';
include_once SYSTEM . 'PHPMailerAutoload.php';

this::init()->getContent();

?>