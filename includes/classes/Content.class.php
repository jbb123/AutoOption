<?php

class Content
{
	public function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	public function getContent($pageName)
	{
		
		$sql = "SELECT content_id, page_title, content
				FROM content
				WHERE page_title = %s AND is_active = 1";
		$page = $this->db->safeReadQueryFirst($sql, $pageName);
		
		return $page;
	}
	
}

?>	