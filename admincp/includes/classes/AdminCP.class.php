<?php

class AdminCP
{

public function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	public function getRoles()
	{
		
		$sql = "SELECT r.admin_role_id, r.title
				FROM lunchpad.admin_role as r";
		
		$roles = $this->lp->safeReadQueryAll($sql);
		return $roles;
	}

	
}

?>