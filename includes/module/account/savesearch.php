<?php


include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

$action = $path_split[3];

if ($action == "savesearch" && $_POST['grant'] == 'w' && $_SESSION['user_id'])
{
	if ($_POST['sub_action'] == "Update")
	{
		$dealer->updateSearch($_POST, $_SESSION['user_id']);
		header('Location: /m/account/dashboard/');
	}
	else
	{
		$dealer->saveSearch($_POST, $_SESSION['user_id']);
		header('Location: /m/account/dashboard/');	
	}
	
}

else
{
	header('Location: /m/account/?login_invalid=1');
}


?>


