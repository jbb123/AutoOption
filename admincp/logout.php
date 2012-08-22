<?php
session_start();

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

define('THIS_SCRIPT', $_SERVER['PHP_SELF']);
define('TIME_NOW', time());




	define('PATH_INCLUDE', 'includes/');
	define('PATH_CLASS', PATH_INCLUDE . 'classes/');
	define('ADMIN_PATH_INCLUDE', 'admincp/includes/');
	define('ADMIN_PATH_CLASS', ADMIN_PATH_INCLUDE . 'classes/');
	
	include_once (PATH_CLASS.'Database.class.php');
	$db	= new Database();
	
    define('THIS_SCRIPT', $_SERVER['PHP_SELF']);
    define('TIME_NOW', time());
	
    #require_once(PATH_INCLUDE . 'init.inc.php');
    require_once(PATH_CLASS . 'Error.class.php');
    
    require_once(ADMIN_PATH_INCLUDE . 'classes/AdminFunction.class.php');
    require_once(ADMIN_PATH_INCLUDE . 'classes/AdminAudit.class.php');



AdminAudit::log('LOGOUT');

$_SESSION['user_id'] = 0;
$_SESSION['admin_id'] = 0;
session_destroy();

header('Location: ./');
exit;
?>
