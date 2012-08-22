<?php

if ($_POST['username'])
{
	
	$sql = "SELECT u.user_id, u.username, u.first_name
			FROM users as u
			WHERE u.username = %s AND u.password = %s";
	$user = $db->safeReadQueryFirst($sql, $_POST['username'], md5($_POST['passwd']));
	
	
	$sql = "
        SELECT aa.admin_id AS id, a.admin_id, u.first_name, u.username, u.user_id
        FROM area_admin AS aa
            INNER JOIN admin AS a ON a.admin_id = aa.admin_id
            INNER JOIN users as u on u.user_id = a.user_id AND u.username = %s AND u.password = %s
        WHERE a.is_active = 1
            
    ";
    
	$admin = $db->safeReadQueryFirst($sql, $_POST['username'], md5($_POST['passwd']));
	
	/*
	if ($admin)
	{
		echo 'admin';
		die;
	}
	*/
	
	
	if ($user)
	{
		$_SESSION['user_id'] = $user['user_id'];
		$_SESSION['username'] = $user['username'];
		$_SESSION['first_name'] = $user['first_name'];
		header('Location: /m/account/dashboard/');
	}
	
	if (!$user && !$admin)
	{
		header('Location: /m/account/?login_invalid=1');
	}
}


?>

