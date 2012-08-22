<?php


include_once('init.inc.php');


$path_split = explode('/', $_SERVER['REQUEST_URI']);

//print_r($path_split);


include_once('templates/header.inc.php');

switch ($path_split[1]) 
{
	case 'c':
		require_once('content/index.php');
		break;
	case 'm':
		require_once('module/'.$path_split[2].'/index.php');
		break;
	case 'l':
		require_once('module/listing/index.php');
		break;
	case '':
		require_once('home/index.php');
		break;
	
	
}

include_once('templates/footer.inc.php');

?>
