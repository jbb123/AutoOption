<?php

session_start();
session_regenerate_id();
header('Content-type: text/html; charset=utf-8');

define('TIME_NOW', time());
define('THIS_SCRIPT', $_SERVER['PHP_SELF']);

define('PATH_INCLUDE', $_SERVER['DOCUMENT_ROOT'] . '/includes/');
define('PATH_CLASS', PATH_INCLUDE . 'classes/');
define('ADMIN_PATH_INCLUDE', $_SERVER['DOCUMENT_ROOT'] . '/admincp/includes/');
define('ADMIN_PATH_CLASS', ADMIN_PATH_INCLUDE . 'classes/');
define('SITE_URL', 'http://68.108.28.197:8080/auto');


$activeDrop = array();
$activeDrop[0] = 'Not Active';
$activeDrop[1] = 'Active';

$yesNoDrop = array();
$yesNoDrop[0] = 'No';
$yesNoDrop[1] = 'Yes';



include_once (PATH_CLASS.'Database.class.php');
$db	= new Database();


require_once(ADMIN_PATH_CLASS . 'AdminFunction.class.php');
require_once(ADMIN_PATH_CLASS . 'AdminAudit.class.php');
require_once(ADMIN_PATH_CLASS . 'AdminRequire.class.php');

require_once(ADMIN_PATH_INCLUDE . 'auth.inc.php');
require_once(PATH_INCLUDE . 'functions.inc.php');


require_once(PATH_CLASS . 'Error.class.php');



?>