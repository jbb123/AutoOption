<?php
class AdminFunction
{
    public static function hasAccess($fn)
    {
    	if ($_SESSION['admin_id'] == 0)
        {
            return false;
        }
		
		if(self::hasEnabledMasks($_SESSION['admin_id'], $fn))
		{
			return true;
		}
		elseif(self::hasDisabledMasks($_SESSION['admin_id'], $fn))
		{
			return false;
		}
		else
		{
			$fn_table = $_SESSION['area_id'] == 0 ? 'admin_role_fn_global' : 'admin_role_fn';
			
	        $sql = "
	            SELECT fn.admin_fn_id AS id
	            FROM admin AS a
	                INNER JOIN area_admin AS aa ON aa.admin_id = a.admin_id
	                INNER JOIN {$fn_table} AS arf ON arf.admin_role_id = aa.admin_role_id
	                INNER JOIN admin_fn AS fn ON fn.admin_fn_id = arf.admin_fn_id
	            WHERE a.admin_id = %d
	                AND a.is_active = 1
	                AND aa.area_id = %d
	                AND fn.`key` = %s
	                AND fn.enabled = 1
	        ";
	        
        	return (bool)$GLOBALS['db']->safeReadQueryFirst($sql, $_SESSION['admin_id'], $_SESSION['area_id'], $fn);
		}
    }

    public static function requireAccess($fn)
    {
        if (!self::hasAccess($fn))
        {
            die('You do not have access to this section of the control panel. (' . $fn . ')');
        }
    }

    public static function getFunctionId($fn)
    {
        static $fn_ids;

        if (!isset($fn_ids[$fn]))
        {
            $sql = "
                SELECT admin_fn_id AS id
                FROM admin_fn
                WHERE `key` = %s
            ";
            $row = $GLOBALS['db']->safeReadQueryFirst($sql, $fn);
            $fn_ids[$fn] = (int)$row['id'];
        }

        return $fn_ids[$fn];
    }

    public static function getFunctionList($area_id, $admin_id)
    {
        static $fns;
        
        $fn_table = $area_id == 0 ? 'admin_role_fn_global' : 'admin_role_fn';

        if (!isset($fns))
        {
            $sql = "
                SELECT af.admin_fn_id AS id, af.name, af.`key`, afg.title AS group_title
                FROM admin_fn AS af
                    INNER JOIN {$fn_table} AS arf ON arf.admin_fn_id = af.admin_fn_id
                    INNER JOIN admin_fn_group AS afg ON afg.admin_fn_group_id = af.admin_fn_group_id
                    INNER JOIN area_admin AS aa ON aa.admin_role_id = arf.admin_role_id
                WHERE aa.area_id = %d
                    AND aa.admin_id = %d
                ORDER BY afg.sequence, af.sequence, af.name
            ";
            $fns = $GLOBALS['db']->safeReadQueryAll($sql, $area_id, $admin_id);
            
        }

        return $fns;
    }

    public static function getFunctionIdList($area_id, $admin_id)
    {
    	
        $fns = self::getFunctionList($area_id, $admin_id);
		
        if ($fns)
        {
            foreach ($fns as $fn)
            {
                $fn_list .= !empty($fn_list) ? ',' : '';
                $fn_list .= (int)$fn['id'];
            }
        }
        else
        {
        	$fn_list = 0;
        }
		
        return $fn_list;
    }

	public static function roleHasAccess($fn, $role)
	{
		$sql = "
            SELECT fn.admin_fn_id AS id
                FROM admin_role_fn AS arf
                	INNER JOIN admin_fn AS fn ON fn.admin_fn_id = arf.admin_fn_id
				WHERE arf.admin_role_id = %d
                AND fn.`key` = %s
                AND fn.enabled = 1
        ";
        return (bool)$GLOBALS['db']->safeReadQueryFirst($sql, $role, $fn);
	}

	public static function myRoleId($admin_id, $area_id)
	{
		$sql = "
			SELECT aa.admin_role_id
			FROM area_admin AS ma
			WHERE aa.admin_id = %d
			AND aa.area_id = %d
		";

		$role_id = $GLOBALS['db']->safeQueryFirst($sql, $admin_id, $area_id);

		return $role_id['admin_role_id'];
	}

	public static function getMaskedFunctionIdList($area_id, $admin_id, $class)
    {
        if (!isset($fns))
        {
            $sql = "
                SELECT arfm.admin_fn_id AS id
                FROM admin_role_fn_mask AS arfm
                WHERE arfm.admin_id = %d
					AND arfm.class = %s
				GROUP BY arfm.admin_fn_id
            ";

            $fns = $GLOBALS['db']->safeReadQueryAll($sql, $admin_id, $class);
        }

		if ($fns)
        {
            foreach ($fns as $fn)
            {
                $fn_list .= !empty($fn_list) ? ',' : '';
                $fn_list .= (int)$fn['id'];
            }
        }

        return $fn_list;
    }

	public static function hasEnabledMasks($admin_id, $fn)
	{

		$sql = "SELECT arfm.admin_fn_id AS id
				FROM admin_role_fn_mask AS arfm
					INNER JOIN admin_fn AS af ON af.admin_fn_id = arfm.admin_fn_id
				WHERE arfm.class = 'enabled'
				AND af.key = %s
				AND arfm.admin_id = %d
		";

		return (bool)$GLOBALS['db']->safeReadQueryFirst($sql, $fn, $admin_id);
	}

	public static function hasDisabledMasks($admin_id, $fn)
	{

		$sql = "SELECT arfm.admin_fn_id AS id
				FROM admin_role_fn_mask AS arfm
					INNER JOIN admin_fn AS af ON af.admin_fn_id = arfm.admin_fn_id
				WHERE arfm.class = 'disabled'
				AND af.key = %s
				AND arfm.admin_id = %d
		";

		return ($GLOBALS['db']->safeReadQueryFirst($sql, $fn, $admin_id));

	}
}
?>