<?php
class AdminRequire
{
    public static function createdProfile()
    {
		$sql = "SELECT * FROM gb.admin
					WHERE user_id = %d";
					
		$admin = $GLOBALS['db']->safeQueryFirst($sql, $_SESSION['user_id']);
		
		if(!$admin['last_name'] || !$admin['address'] || !$admin['city'] || !$admin['state'] || !$admin['postal_code'] || !$admin['country'])
		{
			return false;
		}
		elseif($admin['has_profile'] == 1)
		{
			return true;
		}
		else
		{
			return true;
		}
    }
	
	public static function hasSecurityQuestion()
	{
		$sql = "SELECT has_question
				FROM gb.admin
				WHERE admin_id = %d
		";
		
		$security = $GLOBALS['db']->safeQueryFirst($sql, $_SESSION['admin_id']);
		
		if($security['has_question'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function hasPin()
	{
		$sql = "
			SELECT 1 AS has_pin
			FROM gb.admin
			WHERE admin_id = %d
				AND	pin IS NOT NULL
		"; 
		
		return $has_pin = $GLOBALS['db']->safeQueryFirst($sql, $_SESSION['admin_id']);
	}
}
?>