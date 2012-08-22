 <?php
include_once('includes/init.inc.php');

AdminFunction::requireAccess('ADMIN_LIST');

function save()
{
    global $db;

    $success = false;

    if ($_POST['grant'] != 'w')
    {
        return false;
    }

    if ($_POST['action'] == 'create')
    {
        if (empty($_POST['username'])) Error::raise("You must provide the admin's username.");
    }
    
    $is_area_set = strlen($_POST['new_area_id']) > 0;

    if ($_POST['action'] == 'create' || ($_POST['action'] == 'edit' && $is_area_set))
    {
        if (!$is_area_set) Error::raise("You must select a area.");
        if (empty($_POST['new_admin_role_id'])) Error::raise("You must select an admin role.");
        
    }

    // retrieve all functions this user has access to for this game
    $sql = "
        SELECT DISTINCT arf.admin_fn_id AS id
        FROM admin AS a
            INNER JOIN area_admin AS aa ON aa.admin_id = a.admin_id
            INNER JOIN admin_role_fn AS arf ON arf.admin_role_id = ma.admin_role_id
        WHERE a.user_id = %d
            AND ma.area_id = %d
    ";
    $user_functions = $db->safeReadQueryAll($sql, $_SESSION['admin_id'], $_SESSION['area_id']);

    // build list of function ids
    foreach ($user_functions as $uf)
    {
        $user_function_ids .= $uf['id'] . ',';
    }
    if (!empty($user_function_ids))
    {
        $user_function_ids = substr($user_function_ids, 0, strlen($user_function_ids) - 1);
    }

    // verify grantor is not assigning a role with more permissions then they have
    $sql = "
        SELECT COUNT(admin_fn_id) AS `count`
        FROM admin_role_fn
        WHERE admin_role_id = '" . (int)$_POST['new_admin_role_id'] . "'
            AND admin_fn_id NOT IN ($user_function_ids)
    ";
    $row_functions = $db->safeReadQueryFirst($sql);

    if ($row_functions['count'] > 0)
    {
        Error::raise("You can't assign a role higher than your own to any admin.");
    }

    if (Error::hasErrors())
    {
        return false;
    }

    switch ($_REQUEST['action'])
    {
        case 'create':
			AdminFunction::requireAccess('ADMIN_ADD');
			
            $sql = "
                SELECT u.user_id AS id, a.admin_id
                FROM users AS u
                    LEFT JOIN admin AS a ON a.user_id = u.user_id
                WHERE u.username = %s
            ";
            $user = $db->safeReadQueryFirst($sql, $_POST['username']);
			
            if (!$user)
            {
                Error::raise("Username not found.");
                return false;
            }
			 
            if (empty($user['admin_id']))
            {
                $sql = "
                    INSERT INTO admin (user_id, create_time, modify_time, is_public, is_active)
                    VALUES (%d, %d, %d, %d, %d)
                ";
                $db->safeQuery($sql, $user['id'], TIME_NOW, $_POST['first_name'], $_POST['is_public'], 1);
                $admin_id = (int)$db->insertId();
                
            }
            elseif ($user['admin_id'])
            {
            	$sql = "
            		UPDATE admin SET is_active = 1
            		WHERE admin_id = %d
            	";
            	
            	$db->safeQuery($sql, $user['admin_id']);
                $admin_id = (int)$user['admin_id'];
            }

            if ($admin_id > 0)
            {
                $success = true;

                if ($_POST['new_area_id'] == 'all')
                {
                    $sql = "
                        INSERT INTO area_admin (area_id, admin_id, admin_role_id, position)
                        SELECT a.area_id, %d, %d, %s
                        FROM area AS a
                        WHERE a.is_active = 1
                    ";
                    $db->safeQuery($sql, $admin_id, $_POST['new_admin_role_id'], $_POST['new_position']);
					AdminAudit::log('ADMIN_ADD',$admin_id,'Area(s): All Active');
                }
                else
                {
                    $sql = "
                        INSERT INTO area_admin (area_id, admin_id, admin_role_id, position)
                        VALUES (%d, %d, %d, %s)
                    ";
                    $db->safeQuery($sql, $_POST['new_area_id'], $admin_id, $_POST['new_admin_role_id'], $_POST['new_position']);
					
					$sql = "
						SELECT COALESCE(a.title, 'Global (No-Area)') AS title, COALESCE(pl.nickname, 'None') AS nickname
							FROM area AS a
							WHERE a.area_id = %d";
					
					$area_title = $db->safeReadQueryFirst($sql, $_POST['new_area_id']);
					
					AdminAudit::log('ADMIN_ADD',$admin_id,'Area(s): ' . $area_title['title'] . ' (' . $area_title['nickname'] . ')');
                }
            }
            break;
		
		
        case 'edit':
        
            $sql = "UPDATE admin 
						SET modify_time = %d
                	WHERE admin_id = %d";
                	
            $db->safeQuery($sql, TIME_NOW, $_POST['admin_id']);
			
            $success = (bool)$db->affectedRows();
			
			if ($_POST['new_area_id'] == 'is_global')
			{
				$sql = "SELECT aa.area_id,a.is_active
						FROM area_admin AS aa
							LEFT JOIN area AS a ON a.area_id = aa.area_id
						WHERE aa.admin_id = %d";
				
				$area_ids = array();
				
				$current_areas = $db->safeReadueryAll($sql, $_POST['admin_id']);
				
				foreach($current_areas as $area)
				{
					if($area['is_active'] == 0 && $area['area_id'] > 0)
					{
						$sql = "DELETE FROM area_admin
								WHERE admin_id = %d
									AND area_id = %d";
						$db->safeQuery($sql, $_POST['admin_id'], $area['area_id']);
					}
					array_push($area_ids, $area['area_id']);
					$has_current = join(',', $area_ids);
				}
				
				$sql = "
                        INSERT INTO area_admin (area_id, admin_id, admin_role_id, position)
                        SELECT a.area_id, %d, %d, %s
                        FROM area AS a
                        WHERE a.is_active = 1
						AND a.is_visible = 1
						AND a.platform_id > 0
						AND a.area_id NOT IN ({$has_current})
                    ";
				$db->safeQuery($sql, $_POST['admin_id'], $_POST['new_admin_role_id'], $_POST['new_position']);
				
				AdminAudit::log('ADMIN_ADD',$_POST['admin_id'],'area(s): Updated All Active');
				
			}

            elseif ($is_area_set)
            {
                $sql = "
                    INSERT INTO area_admin (area_id, admin_id, admin_role_id, position)
                    VALUES (%d, %d, %d, %s)
                ";
                $db->safeQuery($sql, $_POST['new_area_id'], $_POST['admin_id'], $_POST['new_admin_role_id'], $_POST['new_position']);
				
					$sql = "
						SELECT COALESCE(a.title, 'Global (No-area)') AS title
							FROM area AS a
							WHERE a.area_id = %d";
					
					$area_title = $db->safeReadQueryFirst($sql, $_POST['new_area_id']);
					
					AdminAudit::log('ADMIN_ADD',$_POST['admin_id'],'area(s): ' . $area_title['title'] . ' (' . $area_title['nickname'] . ')');
            }

            if (is_array($_POST['update']))
            {
			AdminFunction::requireAccess('ADMIN_UPDATE');
                foreach ($_POST['update'] as $area_id)
                {
					$sql = "
						SELECT COUNT(admin_fn_id) AS `count`
						FROM admin_role_fn
						WHERE admin_role_id = '" . (int)$_POST['admin_role_id'][$area_id] . "'
							AND admin_fn_id NOT IN ($user_function_ids)
					";
					$row_functions = $db->safeReadQueryFirst($sql);
				
					if ($row_functions['count'] > 0)
					{
						Error::raise("You can't assign a role higher than your own to any admin.");
					}
					else
					{
						$sql = "
							UPDATE area_admin SET
								admin_role_id = %d,
								position = %s,
								
								sequence = %d
							WHERE area_id = %d
								AND admin_id = %d
						";
						$db->safeQuery($sql,
							$_POST['admin_role_id'][$area_id],
							$_POST['position'][$area_id],
							$_POST['sequence'][$area_id],
							$area_id,
							$_POST['admin_id']
						);
						
						$sql = "
							SELECT COALESCE(a.title, 'Global (No-area)') AS title
								FROM area AS a
								WHERE a.area_id = %d";
						
						$area_title = $db->safeReadQueryFirst($sql, $area_id);
						
						AdminAudit::log('ADMIN_UPDATE',$_POST['admin_id'],'area(s): ' . $area_title['title']);
					}
                }
            }

            if (is_array($_POST['delete']))
            {
			AdminFunction::requireAccess('ADMIN_DELETE');
                foreach ($_POST['delete'] as $area_id)
                {
                    $sql = "
                        DELETE FROM area_admin
                        WHERE area_id = %d
                            AND admin_id = %d
                    ";
                    $db->safeQuery($sql,
                        $area_id,
                        $_POST['admin_id']);
						
						$sql = "
						SELECT COALESCE(a.title, 'Global (No-area)') AS title
							FROM area AS a
							WHERE a.area_id = %d";
					
					$area_title = $db->safeReadQueryFirst($sql, $area_id);
					
					AdminAudit::log('ADMIN_DELETE',$_POST['admin_id'],'area(s): ' . $area_title['title']);
                }
            }
            break;
			
			case 'copy':
			
			
			
			if ($_POST['admin_copy'])
			{
			$sql="SELECT aa.admin_id
				  FROM area_admin as aa
				  WHERE aa.admin_id=%d";
				  
			$admin_info=$db->safeReadQueryFirst($sql, $_POST['admin_id']);
			
			$sql = "DELETE FROM area_admin
					WHERE admin_id = %d";
			$db->safeQuery($sql, $_POST['admin_id']);
			
			$sql="SELECT aa.area_id, aa.admin_id, aa.admin_role_id, aa.position, aa.sequence, aa.is_dupe
				  FROM area_admin as aa
				  WHERE admin_id=%d";
			$area_access = $db->safeReadQueryAll($sql, $_POST['admin_copy']);
			
				foreach ($area_access as $a)
				{
				
				 $sql = "
                    INSERT INTO area_admin (area_id, admin_id, admin_role_id, is_dupe, position)
                    VALUES (%d, %d, %d, %d, %s)
                ";
                
				$db->safeQuery($sql, $a['area_id'], $admin_info['admin_id'], $a['admin_role_id'], $a['is_dupe'], $admin_info['position']);
				
				}
			
			}
			
			break;
    }

    return $success;
}

$form_query = false;

switch ($_REQUEST['action'])
{
    case 'create':
        $display = 'form';
        $action_title = 'Create New Admin';
        if (save())
        {
           showManageRedirect($_SESSION['area_title'] . ' Admin was successfully created.  Redirecting to admin list...', THIS_SCRIPT);
        }
        break;

    case 'edit':
        $display = 'form';
        $action_title = 'Update Admin';
        if (save())
        {
       //   showManageRedirect($_SESSION['area_title'] . ' Admin was successfully updated.  Redirecting to admin list...', THIS_SCRIPT . '?action=edit&admin_id=' . $_REQUEST['admin_id']);
        }
        break;

	case 'copy':
        $display = 'form';
        $action_title = 'Update Admin';
        if (save())
        {
       //   showManageRedirect($_SESSION['area_title'] . ' Admin was successfully updated.  Redirecting to admin list...', THIS_SCRIPT . '?action=edit&admin_id=' . $_REQUEST['admin_id']);
        }
        break;
	
	case 'staff_profile':
		$display = 'profile';
		$action_title = 'Edit Staff Profile';
		break;
	
	case 'edit_profile':
		if (save())
		{
			showManageRedirect($_SESSION['area_title'] . ' Admin was successfully updated. Redirecting to admin list...', THIS_SCRIPT);
		}
		break;
		
    case 'delete':
		AdminFunction::requireAccess('ADMIN_DELETE');
		
        $db->safeQuery("DELETE FROM area_admin WHERE admin_id = %d", $_REQUEST['admin_id']);
        $db->safeQuery("DELETE FROM admin WHERE admin_id = %d", $_REQUEST['admin_id']);
		
		AdminAudit::log('ADMIN_DELETE',$_REQUEST['admin_id'],'area(s): ' . 'All Access');

        if ($db->affectedRows())
        {
            showManageRedirect($_SESSION['area_title'] . ' Admin was successfully deleted.  Redirecting to admin list...', THIS_SCRIPT);
        }
        break;

    default:
        $display = 'list';
        break;
}

if ($display == 'form')
{
    if (isset($_REQUEST['admin_id']))
    {
        $sql = "
            SELECT a.admin_id, a.user_id, u.first_name, u.last_name, u.username, u.email
            FROM admin AS a
                INNER JOIN users AS u ON u.user_id = a.user_id
            WHERE a.admin_id = %d
        ";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['admin_id']);

        $sql = "
            SELECT aa.area_id AS id, aa.admin_id, aa.admin_role_id, aa.position, aa.sequence, a.area_name
            FROM area_admin AS aa
                LEFT JOIN areas AS a ON a.area_id = aa.area_id
            WHERE aa.admin_id = %d
            ORDER BY a.area_name
        ";
        $admin_areas = $db->safeReadQueryAll($sql, $_REQUEST['admin_id']);
		
    }
    
    $sql = "
        SELECT admin_role_id AS id, title
        FROM admin_role
        ORDER BY title
    ";
    $res = $db->safeReadQueryAll($sql);
	
	foreach ($res as $r)
	{
		$admin_roles[$r['id']] = $r['title'];
	}
	    

    if (isset($_POST['action']))
    {
        $fields = array_merge((array)$fields, $_POST);
    }

    $fields = htmlEncode($fields);
}
else
{
    if (isset($_REQUEST['filter_area_id']))
    {
        if ($_REQUEST['filter_area_id'] != 'all')
        {
            $where_area = "AND aa.area_id = " . (int)$_REQUEST['filter_area_id'];
        }
    }
    else
    {
        $where_area = "AND aa.area_id = " . (int)$_SESSION['area_id'];
    }

    $sql = "
        SELECT a.admin_id AS id, a.user_id, u.first_name, u.last_name, aa.im_protocol, aa.im_handle,
            COUNT(aa.area_id) AS num_areas,
            u.username AS username
        FROM admin AS a
            LEFT JOIN area_admin AS aa ON aa.admin_id = a.admin_id
            INNER JOIN users AS u ON u.user_id = a.user_id
        WHERE 1
            $where_area
        GROUP BY a.admin_id
        ORDER BY u.username
    ";
    $admins = $db->safeReadQueryAll($sql);
    
}

$sql = "
    SELECT a.area_id AS id, a.title
    FROM area AS a
    WHERE a.is_active = 1
    ORDER BY a.title
";
$res = $db->safeReadQueryAll($sql);

$areas[0] = 'Global (No-area)';

if ($_REQUEST['action'] == 'create' || $display != 'form')
{
    $areas['all'] = 'All Active';
}

if ($_REQUEST['action'] == 'edit')
{
	$areas['is_global'] = 'Update Active areas';
}

foreach ($res as $r)
{
	$areas[$r['id']] = '(' . $r['platform'] . ') ' .$r['title'];
}


$sql = "SELECT a.admin_id, u.user_id, u.username, aa.area_id
			FROM admin AS a
			LEFT JOIN area_admin AS aa ON aa.admin_id = a.admin_id
			INNER JOIN users AS u ON u.user_id = a.user_id
			WHERE aa.area_id != 'null'
			GROUP BY a.admin_id
			ORDER BY u.username";
$active_admins = $db->safeReadQueryAll($sql);

foreach ($active_admins as $a)
{
	$admin_original[$a['admin_id']] = $a['username'];
}


include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

if ($display == 'form')
{
?>


<script language="javascript">

function flagForDelete(obj, status, id) {
	obj.checked = status;
	obj.parentNode.parentNode.className = status ? "flag-delete" : "";
	document.getElementById("update_" + id).checked = false;
}

function flagForUpdate(obj, status, id) {
	obj.checked = status;
	obj.parentNode.parentNode.className = status ? "flag-update" : "";
	document.getElementById("remove_" + id).checked = false;
}

function checkalldel()
	{
	<?php 
	foreach ($admin_areas as $area)
	            {
				echo "document.getElementById('remove_" .$area['id'] ."').checked = true; \n";

				}
	?>	
	}
function uncheckalldel()
	{
	<?php 
	foreach ($admin_areas as $area)
	            {
				echo "document.getElementById('remove_" .$area['id'] ."').checked = false; \n";

				}
	?>	
	}
</script>

<h1><?php echo $action_title; ?>: <?php echo $fields['username']; ?></h1>
<?php echo Error::getErrorList(); ?>


<form action="<?php echo THIS_SCRIPT; ?>" method="post">
	<input type="hidden" name="action" value="copy" />
    <input type="hidden" name="grant" value="w" />
	<input type="hidden" name="admin_id" value="<?php echo $fields['admin_id']; ?>" />
	
	<fieldset>
        <legend>Copy Admin Access From</legend>
		<div class="lbl">Admin</div><div class="data"><select name="admin_copy"><?php echo getFormOptions($admin_original, $fields['admin_copy']); ?></select></div><br />		
	</fieldset>
<input type="submit" value="<?php echo $action_title; ?>" style="float:right;"/><br />

</form>

<form action="<?php echo THIS_SCRIPT; ?>" method="post">
    <input type="hidden" name="action" value="<?php echo $_REQUEST['action']; ?>" />
    <input type="hidden" name="grant" value="w" />
	<input type="hidden" name="admin_id" value="<?php echo $fields['admin_id']; ?>" />

    <div class="req">Required Field</div><br />

    <fieldset>
        <legend>General Information</legend>
        <?php
        if ($_REQUEST['action'] == 'create')
        {
            echo '<div class="lbl req">Username</div><div class="data"><input type="text" name="username" maxlength="20" value="' . $fields['username'] . '" /></div><br />';
        }
        else
        {
            echo '<div class="lbl">Username</div><div class="data">' . $fields['username'] . '</div><br />';
            echo '<div class="lbl">Email</div><div class="data">' . $fields['email'] . '</div><br />';
        }
        ?>
		<div class="lbl req">First Name</div><div class="data"><?php echo $fields['first_name']; ?></div><br />
    
    </fieldset><br />

    <fieldset>
        <legend>Add area Access</legend>
        <div class="lbl req">area</div><div class="data"><select name="new_area_id" class="lg"><?php echo getFormOptions($areas, $fields['new_area_id']); ?></select></div><br />
		<div class="lbl req">Role</div><div class="data"><select name="new_admin_role_id"><?php echo getFormOptions($admin_roles, $fields['new_admin_role_id']); ?></select></div><br />
        <div class="lbl">Position</div><div class="data"><input type="text" name="new_position" value="<?php echo $fields['new_position']; ?>" /></div><br />
    </fieldset>
	
	
	
	
    <?php
    if ($_REQUEST['action'] == 'edit')
    {
    ?>
    
	<p align="right"><a href="#" OnClick="checkalldel();">Check All Delete</a> | <a href="#" OnClick="uncheckalldel();">UnCheck All Delete</a></p>
    <fieldset>
        <legend>Edit Area Access</legend>
        <table class="list">
            <tr>
                <th>Area</th>
                <th>Role</th>
                <th>Sequence</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
            <?php
            $row = 0;
            foreach ($admin_areas as $area)
            {
                $area = htmlEncode($area);

                $js_text_update = 'onkeypress="flagForUpdate(document.getElementById(\'update_'.$area['id'].'\'), true, '.$area['id'].')"';
                $js_select_update = 'onchange="flagForUpdate(document.getElementById(\'update_'.$area['id'].'\'), true, '.$area['id'].')"';

                echo '<tr class="row' . $row. '">';
                echo '<td class="alt2">' . ($area['id'] > 0 ? $area['area_name'] : 'Global'). '</td>';
                echo '<td class="alt2"><select name="admin_role_id[' . $area['id'] . ']" style="width:auto;" '.$js_select_update.'>' . getFormOptions($admin_roles, $area['admin_role_id']) . '</select></td>';
                echo '<td class="alt1"><input type="text" name="sequence[' . $area['id'] . ']" class="sm" value="' . $area['sequence'] . '" ' . $js_text_update . ' /></td>';
                echo '<td class="alt2"><input type="checkbox" id="update_'.$area['id'].'" name="update[' . $area['id'] . ']" value="' . $area['id'] . '" onchange="flagForUpdate(this,this.checked,'.$area['id'].')" /></td>';
                echo '<td class="alt1"><input type="checkbox" id="remove_'.$area['id'].'"name="delete[' . $area['id'] . ']" value="' . $area['id'] . '" onchange="flagForDelete(this,this.checked,'.$area['id'].')" /></td>';
                echo '</tr>';

                $row = 1 - $row;
            }
            ?>
        </table>
    </fieldset>

    <?php
    }
    ?>

    <a href="<?php echo THIS_SCRIPT; ?>" style="float: left;">Cancel</a>
    <?php
    if ($fields['admin_id']) {
        echo '<a style="color:#c00; padding-left:12px;" href="' . THIS_SCRIPT . '?action=delete&admin_id=' . $fields['admin_id'] . '" onclick="if (!confirm(\'Are you sure you want to delete this admin and all associated area permissions?\')) { return false; }">Delete</a>';
    }
    ?>
    <input type="submit" value="<?php echo $action_title; ?>" style="float:right;"/><br />
    <br />
</form>
<?php
}

else
{
	
    echo '<form method="post">';
    echo '<h1>List <select name="filter_area_id" style="width:auto; height:auto; font-weight:normal;" onchange="this.form.submit();">' . getFormOptions($areas, $_REQUEST['filter_area_id'], false) . '</select> Admins</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Create New Admin</a><br /><br />';
    echo '<select name="keyword" style="width:auto;"><option value="username">Username</option><option value="first_name">First Name</option><option value="last_name">Last Name</option></select> ';
    echo '<select name="criteria" style="width:auto;"><option value="begins">begins with</option><option value="contains">contains</option><option value="ends">ends with</option></select> ';
    echo '<input type="text" name="search" value="' . htmlEncode($_POST['q']) . '" />';
    echo '<input type="submit" value="Search" />';
    echo '</form><br />';

    echo '<table class="list">';
    echo '<tr><th>Username</th><th>Name</th><th>Actions</th></tr>';

    $row = 0;

    foreach ($admins as $admin)
	{
		$sql = "
		SELECT mask_id
		FROM admin_role_fn_mask
		WHERE admin_id = %d
		";
		
		$has_masks = $db->safeReadQueryAll($sql, $admin['id']);
		
	 	if($admin['num_areas'] > 0)
		{
			echo '<tr class="row' . $row . '">';
			echo '<td class="alt1"><a href="' . THIS_SCRIPT . '?action=edit&admin_id=' . $admin['id'] . '">' . htmlEncode($admin['username']) . '</a></td>';
			echo '<td class="alt2">'.$admin['first_name'].' '.$admin['last_name'].'</td>';
			echo '<td class="alt1">';
			echo '<a href="' . THIS_SCRIPT . '?action=edit&admin_id=' . $admin['id'] . '">Edit</a>';
				
			echo ' | <a href="access_masks.php?action=edit&id=' . $admin['id'] . '">Access Masks</a>';
			if($has_masks) { echo '<img src="check.png">'; }
			echo '</td>';
			echo '</tr>';
	
			$row = 1 - $row;
		}
    }

    echo '</table>';
}
include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>