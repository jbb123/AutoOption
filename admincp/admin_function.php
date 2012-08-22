<?php
include_once('includes/init.inc.php');

AdminFunction::requireAccess('ADMIN_FUNCTIONS');

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');
?>
<style>
	.selected {
	color: pink; 
	font-weight: bold; 
	background-color: #2B94ED;
}
</style>
<?php
function save()
{
	global $db;
	
	if ($_POST['grant'] != 'w')
	{
		return false;
	}
	
	switch ($_REQUEST['action'])
	{
		case 'reorder':
			$ids = $_POST['sequence'];
			
			foreach($ids as $fn_id => $seq)
			{
				$sql = "UPDATE admin_fn SET sequence = %d
						WHERE admin_fn_id = %d
				";
				
				$db->safeQuery($sql, $seq, $fn_id);
			}
		showManageRedirect('Sequences successfully updated. Redirecting to admin function list...', THIS_SCRIPT);
		
		break;
		case 'create':
			AdminFunction::requireAccess('ADMIN_FN_CREATE');
			
			$sql = "SELECT MAX(sequence) + 5 AS sequence
					FROM admin_fn
					WHERE admin_fn_group_id = %d
						AND `show` = %d
			";
			
			$max = $db->safeReadQueryFirst($sql, $_POST['admin_fn_group_id'], $_POST['show']);
			
			$sql = "INSERT INTO admin_fn (admin_fn_group_id, `key`, name, enabled, class, `show`, script_name, sequence, reference_type) VALUES(%d, %s, %s, %d, %s, %d, %s, %d, %s)";
			
			$db->safeQuery($sql,
							   $_POST['admin_fn_group_id'],
							   strtoupper($_POST['key']),
							   $_POST['name'],
							   1,
							   $_POST['class'],
							   $_POST['show'],
							   $_POST['script_name'],
							   $max['sequence'],
							   $_POST['reference_type']
							 );
			
			if ($db->affectedRows())
			{
				$admin_fn_id = $db->insertId();
				AdminAudit::log('ADMIN_FN_CREATE', $admin_fn_id);
				
				$sql = "INSERT INTO admin_role_fn (admin_role_id, admin_fn_id) VALUES(%d, %d)";
				
				$db->safeQuery($sql, 1, $admin_fn_id);
				 
				$success = true;
			}
		break;
		case 'edit':
			$sql = "
				UPDATE admin_fn SET admin_fn_group_id = %d, `key` = %s, name = %s, enabled = %d, class = %s, `show` = %d, script_name = %s, sequence = %d, reference_type = %s
				WHERE admin_fn_id = %d
			";
			
			$db->safeQuery($sql,
							   $_POST['admin_fn_group_id'],
							   $_POST['key'],
							   $_POST['name'],
							   $_POST['enabled'],
							   $_POST['class'],
							   $_POST['show'],
							   $_POST['script_name'],
							   $_POST['sequence'],
							   $_POST['reference_type'],
							   $_POST['admin_fn_id']
							  );
			if ($db->affectedRows())
			{
				AdminAudit::log('ADMIN_FN_EDIT', $_POST['admin_fn_id']);
				$success = true;
			}
		break;
		case 'manage':
			$sql = "DELETE FROM admin_role_fn WHERE admin_fn_id = %d";
			
			$db->safeQuery($sql, (int)$_POST['admin_fn_id']);
			
			if ($db->affectedRows())
			{
				foreach ($_POST['admin_role'] as $role_id => $enabled)
				{
					$sql = "INSERT INTO admin_role_fn (admin_role_id, admin_fn_id) VALUES (%d, %d)";
					
					$db->safeQuery($sql, (int)$role_id, (int)$_POST['admin_fn_id']);
				}
			}
			
			if ($db->affectedRows())
			{
				AdminAudit::log('ADMIN_FN_EDIT', (int)$_POST['admin_fn_id'], 'Updated Global Accesses');
				$success = true;
			}
		break;
	}
	
	return $success;		
}

switch ($_REQUEST['action'])
{
	case 'edit':
		$display = 'form';
		$action_title = 'Edit Admin Function';
		if (save())
		{
			showManageRedirect('Admin Function edited successfully. Redirecting to Admin Function List...', THIS_SCRIPT);
		}
	break;
	case 'create':
		$display = 'form';
		$action_title = 'Create Admin Function';
		if (save())
		{
			showManageRedirect('Admin Function created. Redirecting to Admin Function List...', THIS_SCRIPT);
		}
	break;
	case 'manage':
		$display = 'manage';
		$action_title = 'Manage Admin Function';
		if (save())
		{
			showManageRedirect('Admin function access updated. Redirecting to Admin Function List...', THIS_SCRIPT);
		}
	break;
	case 'reorder':
		if (save())
		{
			showManageRedirect('Admin function sequence updated. Redirecting to Admin Function List...', THIS_SCRIPT);
		}
	break;
	default:
		$display = 'list';
		$action_title = 'List Admin Functions';
	break;
}

if ($display == 'form')
{
	$sql = "SELECT af.*
		FROM admin_fn AS af
			INNER JOIN admin_fn_group AS afg ON afg.admin_fn_group_id = af.admin_fn_group_id
		WHERE af.admin_fn_id = %d
		ORDER BY afg.sequence, af.show DESC, af.sequence, af.name
	";
	
	$fn = $db->safeReadQueryFirst($sql, $_REQUEST['id']);
	
	$sql = "SELECT * FROM admin_fn_group
			ORDER BY sequence
	";
	
	$groups = $db->safeReadQueryAll($sql);
	
	$admin_groups = array();
	
	foreach ($groups as $g)
	{
		$admin_groups[$g['admin_fn_group_id']] = $g['title'];
	}	
	
	$classes = array('area' => 'Areas Only', 'global' => 'Global (No-Arena) Only', 'site' => 'All Control Panels');
}
elseif ($display == 'list')
{
		$sql = "SELECT af.admin_fn_id, af.name, af.sequence, af.show, afg.title, afg.admin_fn_group_id, af.enabled, af.key
				FROM admin_fn AS af
					INNER JOIN admin_fn_group AS afg ON afg.admin_fn_group_id = af.admin_fn_group_id
				ORDER BY afg.sequence, af.show DESC, af.sequence, af.name
		";
		
		$fns = $db->safeReadQueryAll($sql);
		
		$sql = "
			SELECT admin_fn_group_id, title
			FROM admin_fn_group
		";
		
		$headers = $db->safeReadQueryAll($sql);
}
elseif ($display == 'manage')
{
	$sql = "
		SELECT ar.admin_role_id, ar.title, IF(arf.admin_role_id = ar.admin_role_id, 1, 0) AS enabled
		FROM admin_role AS ar
			LEFT JOIN admin_role_fn AS arf ON arf.admin_fn_id = %d AND arf.admin_role_id = ar.admin_role_id
		ORDER BY ar.sequence
	";
	
	$roles = $db->safeReadQueryAll($sql, (int)$_REQUEST['id']);
}

if ($display == 'list')
{		
		$last_fn_group_id = -1;
		
		echo '<h1>' . $action_title . '</h1>';
		if (AdminFunction::hasAccess('ADMIN_FN_CREATE'))
		{
			echo '<a href="' . THIS_SCRIPT . '?action=create">Create New Admin Function</a><br /><br />';
		}
		echo '<form action="' . THIS_SCRIPT . '" method="POST">';
		echo '<input type="hidden" name="grant" value="w">';
		echo '<input type="hidden" name="action" value="reorder">';
		
		$row_count = 0;
		
		foreach ($headers as $h)
		{
			echo '<a class="adminTabs" onClick="
					$$(\'table.admin_fn\').each(function(s){s.hide()});
					$(\'' . $h['admin_fn_group_id'] . '\').show(); 
					$$(\'.adminTabs\').each(function(s){s.removeClassName(\'selected\');});
					this.addClassName(\'selected\');"
				  style="cursor:pointer; height: 25px;">' . $h['title'] . '</a> | ';
		}
		echo '<br />';
		
		foreach($fns as $fn)
		{
			
			if ($last_fn_group_id != $fn['admin_fn_group_id'])
            {
                echo '<table class="list admin_fn" id="' . $fn['admin_fn_group_id'] . '">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>Sequence</th>
					<th>Enabled</th>
					<th>Actions</th>
				</tr>
				<tr><th class="category" colspan="8">' . $fn['title'] . '</th></tr>';

                $last_fn_group_id = $fn['admin_fn_group_id'];
            }
			
			$row_class = $row_count % 2 ? 'row0' : 'row1';
			echo '<tr class="' . $row_class . '">';
			echo '<td class="alt1">' . $fn['admin_fn_id'] . '</td>';
			echo '<td class="alt2">'; if($fn['show']) { echo '<font color="#2B94ED">' . $fn['name'] . '</font>'; } else { echo $fn['name']; } echo '</td>';
			echo '<td class="alt1">' . $fn['key'] . '</td>';
			echo '<td class="alt2"><input type="text" class="sm" name="sequence[' . $fn['admin_fn_id'] . ']" value="' . $fn['sequence'] . '"></td>';
			echo '<td class="alt1"><input type="checkbox" name="status[' . $fn['admin_fn_id'] . ']" value="1"'; if($fn['enabled']) { echo ' CHECKED'; } echo '></td>';
			echo '<td class="alt2"><a href="' . THIS_SCRIPT . '?action=edit&id=' . $fn['admin_fn_id'] . '">Edit</a> | <a href="' . THIS_SCRIPT . '?action=manage&id=' . $fn['admin_fn_id'] . '">Manage</a>';
			echo '</tr>';
			$row_count++;
			
			if ($last_fn_group_id != $fn['admin_fn_group_id'])
			{
				echo '</table>';
			}
		}
		echo '<input type="submit" value=" Update " style="float: right;">';
		echo '</form>';
}
elseif ($display == 'manage')
{
	echo '<h1>' . $action_title . '</h1>';
	
	echo '<form action="' . THIS_SCRIPT . '" method="POST">';
	echo '<input type="hidden" name="grant" value="w" />';
	echo '<input type="hidden" name="action" value="manage" />';
	echo '<input type="hidden" name="admin_fn_id" value="' . (int)$_REQUEST['id'] . '" />';
	
	echo '<table class="list">';
		echo '<tr>';	
			echo '<th>Role</th>';
			echo '<th>Has Access</th>';
		echo '</tr>';
		
	$row_count = 0;
	
	foreach ($roles as $r)
	{
		$row_class = $row_count % 2 ? 'row0' : 'row1';
		
		echo '<tr class="' . $row_class . '">';
			echo '<td class="alt1">' . $r['title'] . '</td>';
			echo '<td class="alt2"><input type="checkbox" name="admin_role[' . $r['admin_role_id'] . ']"'; if ($r['enabled']) { echo ' CHECKED'; } echo '></td>';
		echo '</tr>';
		
		$row_count++;
	}
	
	echo '</table>';
	echo '<input type="submit" value="Edit Role Function Access" style="float: right;">';
	echo '</form>';
}
elseif ($display == 'form')
{
	echo '<h1>' . $action_title . '</h1>';
	
	echo '<form action="' . THIS_SCRIPT . '" method="POST">';
	echo '<input type="hidden" name="grant" value="w">';
	echo '<input type="hidden" name="action" value="' . $_REQUEST['action'] . '">';
	echo '<input type="hidden" name="admin_fn_id" value="' . (int)$fn['admin_fn_id'] . '">';
	
	echo '<fieldset>';
		echo '<legend>Position Information</legend>';
		
		echo '<div class="lbl req">Group</div><div class="data"><select name="admin_fn_group_id">' . getFormOptions($admin_groups, $fn['admin_fn_group_id']) . '</select></div><br />';
		echo '<div class="lbl req">Key</div><div class="data"><input type="text" name="key" value="' . $fn['key'] . '"></div><br />';
		echo '<div class="lbl req">Name</div><div class="data"><input type="text" name="name" value="' . $fn['name'] . '"></div><br />';
		echo '<div class="lbl req">Class</div><div class="data"><select name="class">' . getFormOptions($classes, $fn['class']) . '</select></div><br />';
		echo '<div class="lbl req">Script</div><div class="data"><input type="text" name="script_name" value="' . $fn['script_name'] . '"></div><br />';
		echo '<div class="lbl req">Sequence</div><div class="data"><input type="text" class="sm" name="sequence" value="' . $fn['sequence'] . '"></div><br />';
		echo '<div class="lbl">Reference Type</div><div class="data"><input type="text" name="reference_type" value="' . $fn['reference_type'] . '"></div><br />';
		echo '<div class="lbl req">Show in Nav</div><div class="data"><select name="show" class="sm"><option value="0">No</option><option value="1" ';
		if ($fn['show'])
		{
			echo 'SELECTED';
		}
		echo '>Yes</option></select></div><br />';
		echo '<div class="lbl req">Enabled</div><div class="data"><select name="enabled" class="sm"><option value="0">No</option><option value="1" ';
		if ($fn['enabled'])
		{
			echo 'SELECTED';
		}
		echo '>Yes</option></select></div><br />';
		
	echo '</fieldset>';
	
	echo '<input type="submit" value="' . $action_title . '" style="float: right;">';
	echo '</form>';
}
include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');

?>
		