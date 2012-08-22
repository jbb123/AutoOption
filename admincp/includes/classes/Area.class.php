<?php


class Area
{
	
	public function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
    public function getInfo()
    {
    	    	
    	$sql = "SELECT a.user_id, a.area_name, a.area_id
				FROM areas AS a
				WHERE a.area_id = %d";
		
        $areaInfo = $this->db->safeReadQueryFirst($sql, $_SESSION['area_id']);
        
        return $areaInfo;
   
    }
    
}
?>
