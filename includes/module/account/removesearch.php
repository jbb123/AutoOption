<?php
include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

$action = $path_split[3];

if ($action == "removesearch" && $_SESSION['user_id'])
{
	$dealer->removeSearch($_REQUEST['search_id'], $_SESSION['user_id']);
	header('Location: /m/account/dashboard/');
}

else
{
	header('Location: /m/account/?login_invalid=1');
}


?>


