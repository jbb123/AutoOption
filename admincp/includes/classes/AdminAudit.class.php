<?php

class AdminAudit
{
    public static function log($fn, $reference_id=0, $details=false)
    {
    	global $db;
    	
        $fn_id = AdminFunction::getFunctionId($fn);
        
        if ($fn_id)
        {
            $sql = "
                INSERT DELAYED INTO gb.admin_audit (area_id, user_id, admin_id, admin_fn_id, ip, audit_time, reference_id, details)
                VALUES (%d, %d, %d, %d, %d, %d, %d, %S)
            ";
            $db->safeQuery($sql, $_SESSION['area_id'], $_SESSION['user_id'], $_SESSION['admin_id'], $fn_id, ip2long($_SERVER['REMOTE_ADDR']), TIME_NOW, $reference_id, $details);
        }
   
    }
    
}
?>
