<?php


include_once('includes/init.inc.php');

AdminFunction::requireAccess('ROLE_LIST');

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');


function save()
{
    global $db;

    $success = false;

    if ($_POST['grant'] != 'w')
    {
		return false;
    }
	
    if ($_POST['action'] != 'delete')
    {
        if (empty($_POST['title'])) Error::raise("You must provide a title.");
    }

    if (Error::hasErrors())
    {
		echo Error::getErrorList();
        return false;
    }

    switch ($_REQUEST['action'])
    {
        case 'create':
            AdminFunction::requireAccess('ROLE_CREATE');
			
			//$role_fn_table = $_SESSION['area_id'] == 0 ? 'admin_role_fn_global' : 'admin_role_fn';

            if ((int)$_POST['sequence'] > 0)
            {
                $sequence = (int)$_POST['sequence'];
            }
            else
            {
                $row = $db->queryFirst("SELECT MAX(sequence)+1 AS next_sequence FROM admin_role");
                $sequence = $row['next_sequence'];
            }

            $sql = "
                INSERT INTO admin_role (title, class, sequence)
                VALUES (%s, %s, %d)
            ";
            $db->safeQuery($sql, $_POST['title'], $_POST['class'], $sequence);
            $success = (bool)$db->affectedRows();
            $admin_role_id = $db->insertId();

            if (count((array)$_POST['functions']) > 0)
            {
                $insert_sql = "
                    INSERT INTO admin_role_fn (admin_role_id, admin_fn_id) VALUES
                ";
                foreach ($_POST['functions'] as $f_id)
                {
                    $insert_sql .= "(" . (int)$admin_role_id . ", " . (int)$f_id . "),";
                }
                $insert_sql = substr($insert_sql, 0, strlen($insert_sql)-1);

                $db->query($insert_sql);
            }
   			if (count((array)$_POST['gfunctions']) > 0)
            {
                $insert_sql = "
                    INSERT INTO admin_role_fn_global (admin_role_id, admin_fn_id) VALUES
                ";
                foreach ($_POST['gfunctions'] as $f_id)
                {
                    $insert_sql .= "(" . (int)$admin_role_id . ", " . (int)$f_id . "),";
                }
                $insert_sql = substr($insert_sql, 0, strlen($insert_sql)-1);

                $db->query($insert_sql);

                $success = $success || (bool)$db->affectedRows();
            }

            AdminAudit::log('ROLE_CREATE', $admin_role_id, 'Created role: ' . $_POST['title']);
            break;

        case 'edit':
            AdminFunction::requireAccess('ROLE_EDIT');

            $sql = "
                UPDATE admin_role SET
                    title = %s,
                    class = %s
                WHERE admin_role_id = %d
            ";
            $db->safeQuery($sql, $_POST['title'], $_POST['class'], $_POST['admin_role_id']);
            $success = (bool)$db->affectedRows();
            $admin_role_id = $_POST['admin_role_id'];

            if (count((array)$_POST['functions']) > 0)
            {
                $db->query("
                    DELETE FROM admin_role_fn
                    WHERE admin_role_id = '" . $admin_role_id . "'
                ");

                $insert_sql = "
                    INSERT INTO admin_role_fn (admin_role_id, admin_fn_id) VALUES
                ";
                foreach ($_POST['functions'] as $f_id)
                {
                    $insert_sql .= "(" . (int)$admin_role_id . ", " . (int)$f_id . "),";
                }
                $insert_sql = substr($insert_sql, 0, strlen($insert_sql)-1);

                $db->query($insert_sql);

                $success = $success || (bool)$db->affectedRows();
            }
			
	    if (count((array)$_POST['gfunctions']) > 0)
            {
                $db->query("
                    DELETE FROM admin_role_fn_global
                    WHERE admin_role_id = '" . $admin_role_id . "'
                ");

                $insert_sql = "
                    INSERT INTO admin_role_fn_global (admin_role_id, admin_fn_id) VALUES
                ";
                foreach ($_POST['gfunctions'] as $f_id)
                {
                    $insert_sql .= "(" . (int)$admin_role_id . ", " . (int)$f_id . "),";
                }
                $insert_sql = substr($insert_sql, 0, strlen($insert_sql)-1);

                $db->query($insert_sql);

                $success = $success || (bool)$db->affectedRows();
            }

            AdminAudit::log('ROLE_EDIT', $admin_role_id, 'Edited role: ' . $_POST['title']);
            break;
		
		
        case 'delete':
            AdminFunction::requireAccess('ROLE_DELETE');

            $sql = "
                SELECT title
                FROM admin_role
                WHERE admin_role_id = %d
            ";
            $role = $db->safeReadQueryFirst($sql, $_POST['admin_role_id']);

            $sql = "
                DELETE FROM admin_role_fn WHERE admin_role_id = %d
            ";
            $db->safeQuery($sql, $_POST['admin_role_id']);
            
            $sql = "
            	DELETE FROM admin_role_fn_global WHERE admin_role_id = %d
            ";
            $db->safeQuery($sql, $_POST['admin_role_id']);

            $sql = "
                DELETE FROM admin_role WHERE admin_role_id = %d
            ";
            $db->safeQuery($sql, $_POST['admin_role_id']);
			
            $sql= "
        		DELETE from area_admin
        		WHERE admin_role_id = %d";
        	$db->safeQuery($sql, $_POST['admin_role_id']);
            
            $success = true;

            AdminAudit::log('ROLE_DELETE', $_POST['admin_role_id'], 'Deleted role: ' . $role['title']);
            break;
    }

    return $success;
}

$form_query = false;

switch ($_REQUEST['action'])
{
    case 'create':
        $display = 'form';
        $action_title = 'Create New Admin Role';
        if (save())
        {
            showManageRedirect('Admin role was successfully created.  Redirecting to admin role list...', THIS_SCRIPT);
        }
        break;
	
	
	
    case 'edit':
        $display = 'form';
        $action_title = 'Update Admin Role';
        if (save())
        {
            showManageRedirect('Admin role was successfully updated.  Redirecting to admin role list...', THIS_SCRIPT);
        }
        break;

    case 'delete':
        $display = 'delete';
        $action_title = 'Delete Admin Role';
        if (save())
        {
            showManageRedirect('Admin role was successfully deleted.  Redirecting to admin role list...', THIS_SCRIPT);
        }
        break;

    case 'reorder':
        foreach ($_POST['sequence'] AS $k => $v)
        {
            $sql = "
                UPDATE admin_role SET
                    sequence = " . (int)$v . "
                WHERE admin_role_id = " . (int)$k . "
            ";
            $db->query($sql);
        }
        break;

    default:
        $display = 'list';
        break;
}

if ($display == 'form' || $display == 'delete')
{
    $sql = "
        SELECT admin_role_id, title, class, sequence
        FROM admin_role
        WHERE admin_role_id = %d
    ";
    $fields = $db->safeReadQueryFirst($sql, $_REQUEST['admin_role_id']);

    if (isset($_POST['action']))
    {
        $fields = array_merge((array)$fields, $_POST);
    }

    $fields = htmlEncode($fields);

    $sql = "
        SELECT af.admin_fn_id AS id, af.name, af.show, af.description, af.sequence, afg.admin_fn_group_id, afg.title AS group_title, IF(arf.admin_fn_id IS NULL, 0, 1) AS checked
        FROM admin_fn AS af
            INNER JOIN admin_fn_group AS afg ON afg.admin_fn_group_id = af.admin_fn_group_id
            LEFT JOIN admin_role_fn AS arf ON arf.admin_role_id = %d AND arf.admin_fn_id = af.admin_fn_id
        ORDER BY afg.sequence, af.show DESC, af.sequence
    ";
    $functions = $db->safeReadQueryAll($sql, $_REQUEST['admin_role_id']);
	
	if ($fields['class'] == 'global')
	{
		$sql = "
			SELECT af.admin_fn_id AS id, af.name, af.show, af.description, af.sequence, afg.admin_fn_group_id, afg.title AS group_title, IF(arf.admin_fn_id IS NULL, 0, 1) AS checked
			FROM admin_fn AS af
				INNER JOIN admin_fn_group AS afg ON afg.admin_fn_group_id = af.admin_fn_group_id
				LEFT JOIN admin_role_fn_global AS arf ON arf.admin_role_id = %d AND arf.admin_fn_id = af.admin_fn_id
			WHERE af.class IN ('global','site')
			ORDER BY afg.sequence, af.show DESC, af.sequence
    	";
    	$gfunctions = $db->safeReadQueryAll($sql, $_REQUEST['admin_role_id']);
	}
}

else
{
    $sql = "
        SELECT admin_role_id, title, class, sequence
        FROM admin_role
        ORDER BY class DESC, sequence
    ";
    $admin_roles = $db->SafeReadQueryAll($sql);
}

$classes = array('area'=>'Area','global'=>'Global');

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

if ($display == 'form')
{
?>
<h1><?php echo $action_title; ?></h1>
<?php echo Error::getErrorList(); ?>
<form action="<?php echo THIS_SCRIPT; ?>" method="post" name="role_functions">
    <input type="hidden" name="action" value="<?php echo $_REQUEST['action']; ?>" />
    <input type="hidden" name="grant" value="w" />
	<input type="hidden" name="admin_role_id" value="<?php echo $fields['admin_role_id']; ?>" />

    <div class="req">Required Field</div><br />

    <fieldset>
        <legend>General Information</legend>
        <div class="lbl req">Title</div><div class="data"><input type="text" name="title" maxlength="30" value="<?php echo $fields['title']; ?>" /></div><br />
        <div class="lbl req">Classification</div><div class="data"><select name="class"><?php echo getFormOptions($classes, $fields['class']); ?></select></div><br />
    </fieldset>

	<?php
	if ($fields['class'] == 'global')
	{
	?>
        <fieldset>
            <legend>Global Permissions Information</legend>
            <table cellpadding="3" cellspacing="0" class="list">
            <?php
            $last_fn_group_id = -1;
    
            foreach ($gfunctions as $f)
            {
                if ($last_fn_group_id != $f['admin_fn_group_id'])
                {
                    if ($last_fn_group_id >= 0)
                    {
                        echo '</td></tr>';
                    }
    
                    echo '<tr><th>' . $f['group_title'] . '</th></tr><tr><td>';
    
                    $last_fn_group_id = $f['admin_fn_group_id'];
                    $first = true;
                    $i = 1;
                }
    
                if ($i > 3)
                {
                   $i = 1;
                }
    
                $checked = $f['checked'] ? ' checked' : '';
                echo '<div style="width:200px; float:left;"><input id="gfcn_' . $f['id'] . '" type="checkbox" name="gfunctions[]" value="' . $f['id'] . '"' . $checked . ' /><label for="gfcn_' . $f['id'] . '">';
                
                if($f['show'] == 1)
                {
                    echo '<font color="#2B94ED">' . $f['name'] . '</font></label></div>';
                }
                else
                {
                    echo $f['name'] . '</label></div>';
                }
    
                $i++;
            }
    
            if ($last_fn_group_id >= 0)
            {
                echo '</td></tr>';
            }
            ?>
            </table>
        </fieldset>
    <?php
	}
	?>
	<br />
    <fieldset>
        <legend>Area Permissions Information</legend>
        <table cellpadding="3" cellspacing="0" class="list">
        <?php
        $last_fn_group_id = -1;

        foreach ($functions as $f)
        {
            if ($last_fn_group_id != $f['admin_fn_group_id'])
            {
                if ($last_fn_group_id >= 0)
                {
                    echo '</td></tr>';
                }

                echo '<tr><th>' . $f['group_title'] . '</th></tr><tr><td>';

                $last_fn_group_id = $f['admin_fn_group_id'];
                $first = true;
                $i = 1;
            }

            if ($i > 3)
            {
               $i = 1;
            }

            $checked = $f['checked'] ? ' checked' : '';
            echo '<div style="width:200px; float:left;"><input id="fcn_' . $f['id'] . '" type="checkbox" name="functions[]" value="' . $f['id'] . '"' . $checked . ' /><label for="fcn_' . $f['id'] . '">';
			
			if($f['show'] == 1)
			{
				echo '<font color="#2B94ED">' . $f['name'] . '</font></label></div>';
			}
			else
			{
				echo $f['name'] . '</label></div>';
			}

            $i++;
        }

        if ($last_fn_group_id >= 0)
        {
            echo '</td></tr>';
        }
        ?>
        </table>
    </fieldset>

    <a href="<?php echo THIS_SCRIPT; ?>" style="float: left;">Cancel</a>
    <?php
    if ($fields['admin_role_id']) {
        echo '<a style="color:#c00; padding-left:12px;" href="' . THIS_SCRIPT . '?action=delete&admin_role_id=' . $fields['admin_role_id'] . '">Delete</a>';
    }
    ?>

    <input type="submit" value="<?php echo $action_title; ?>" style="float:right;"/>
</form>
<?php
}


elseif ($display == 'delete')
{
?>
<h1><?php echo $action_title; ?></h1>
<form action="<?php echo THIS_SCRIPT; ?>" method="post">
    <input type="hidden" name="action" value="<?php echo $_REQUEST['action']; ?>" />
    <input type="hidden" name="grant" value="w" />
	<input type="hidden" name="admin_role_id" value="<?php echo $fields['admin_role_id']; ?>" />

    Are you sure you want to delete the admin role titled &quot;<?php echo $fields['title']; ?>&quot;?<br />
    <br />

    <input type="button" value="Yes" onclick="this.form.submit();" />
    <input type="button" value="No" onclick="document.location.href='<?php echo THIS_SCRIPT; ?>';" />
</form>
<?php
}
else
{
    echo '<form action="' . THIS_SCRIPT . '" method="post">';
    echo '<input type="hidden" name="action" value="reorder" />';
    echo '<input type="hidden" name="grant" value="w" />';

    echo '<h1>List Admin Roles</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Create New Admin Role</a> <br /><br />';
    echo '<table class="list">';
    echo '<tr><th>Title</th><th>Classification</th><th>Sequence</th><th>Actions</th></tr>';

    $last_platform_id = 0;

    $row = 0;

    foreach ($admin_roles as $role)
    {
        echo '<tr class="row' . $row . '">';
        echo '<td class="alt1"><a href="'. THIS_SCRIPT . '?action=edit&admin_role_id=' . $role['admin_role_id'] . '">' . $role['title'] . '</a></td>';
        echo '<td class="alt2">' . ucfirst($role['class']) . '</td>';
        echo '<td class="alt2"><input type="text" name="sequence[' . $role['admin_role_id'] . ']" value="' . $role['sequence'] . '" class="sm" /></td>';
        echo '<td class="alt2"><a href="' . THIS_SCRIPT . '?action=edit&admin_role_id=' . $role['admin_role_id'] . '">Edit</a> | <a href="' . THIS_SCRIPT . '?action=create&admin_role_id=' . $role['admin_role_id'] . '">Use as Template</a> | <a href="' . THIS_SCRIPT . '?action=delete&admin_role_id=' . $role['admin_role_id'] . '">Delete</a></td>';
        echo '</tr>';

        $row = 1 - $row;
    }

    echo '</table>';
    echo '<br /><input type="submit" value="Update Order" style="float:right;"/>';
    echo '</form>';
}
include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');

?>
