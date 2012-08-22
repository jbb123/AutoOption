<?php

include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

$action = $path_split[3];

if ($action == "createaccount" && $_POST['grant'] == 'w')
{
	
	$success = $dealer->createAccount($_POST);
	
	if ($success == 1)
	{
		include_once ('login.php');
	}
	else
	{
		header('Location: /m/account/?login_invalid=1');	
	}
}

else
{
	header('Location: /m/account/?login_invalid=1');
}


?>


