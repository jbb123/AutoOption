<?php 
/*
 * Authorize.net
 * Dealer Seller Signup work?
 * Seller Signup work?
 * 
 * Import listings probably isn't importing all rows
 * Graphics on Backend
 * 
 * Move site to another server
 * Styles/css make it look good (Not Me)
 * 
 * 
 */

if (session_id() == "") {session_start();}

if ($_GET['killsession'] == 1)
{

session_unset(); 
session_destroy();
setcookie(session_name(),'');
unset($_COOKIE[session_name()]);

}

define('TIME_NOW', time());
define('PATH_INCLUDE', $_SERVER['DOCUMENT_ROOT'] . '/includes/');
define('PATH_CLASS', PATH_INCLUDE . 'classes/');
define('THIS_SCRIPT', $_SERVER['PHP_SELF']);

include_once('functions.inc.php');

include_once (PATH_CLASS.'Database.class.php');
$db	= new Database();

require_once(PATH_CLASS . 'Error.class.php');



?>