<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('PROMO_LIST');

$errors = array();

function save()
{
    global $db, $errors;

    $success = false;

    if ($_POST['grant'] != 'w')
    {
        return false;
    }
    
    if (in_array($_REQUEST['action'], array('create', 'edit')))
    {
    	if (empty($_POST['name'])) Error::raise("You must provide a promotion name.");
    }
    
    if (Error::hasErrors())
    {
        return false;
    }
 	
    
    switch ($_REQUEST['action'])
    {
        case 'create':
            
            $sql = "
                INSERT INTO promo_codes (name, discount, is_active)
                VALUES (%s, %f, %d)
            ";
            $db->safeQuery($sql,
            				$_POST['name'],
						    $_POST['discount'],
						    $_POST['is_active']
						    );
            $promo_id= (int)$db->insertId();
			
			AdminAudit::log('PROMO_ADD',$promo_id);
		break;
		
        case 'edit':
        	$sql = "
                UPDATE promo_codes SET
                    name = %s,
                    discount = %f,
                    is_active = %d
				WHERE promo_id = %d
            ";
            
            $db->safeQuery($sql,
                $_POST['name'],
                $_POST['discount'],
                $_POST['is_active'],
                $_POST['promo_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('PROMO_UPDATE',$_POST['promo_id']);
            break;
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'create':
      	$display = 'form';
      	$action_title = 'Create New Promotion';
      	if (save())
      	{
      		showManageRedirect('Promotion was successfully created.  Redirecting to Promotion list...', THIS_SCRIPT);
      	}
      break;

    case 'edit':
      	$display = 'form';
      	$action_title = 'Update Promotion';
      	if (save())
      	{
       		showManageRedirect('Promotion was successfully updated.  Redirecting to Promotion list...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

if ($display == 'form')
{
	
    if (isset($_REQUEST['promo_id']))
    {
    	
        $sql = "
    		SELECT p.promo_id, p.name, p.discount, p.is_active
			FROM promo_codes as p
    		WHERE p.promo_id = %d
		";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['promo_id']);
    }
    
    if (isset($_POST['action']))
    {
        $fields = array_merge((array)$fields, $_POST);
    }

    $fields = htmlEncode($fields);
}
else
{
	$sql = "
    	SELECT p.promo_id, p.name, p.discount, p.is_active
			FROM promo_codes as p
    	ORDER BY p.name ASC
    ";
    
    $promos = $db->safeReadQueryAll($sql);
    
}

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

if ($display == 'form')
{
?>

<?php	
	
	
	echo '<h1>'.$action_title .'</h1>';
	echo Error::getErrorList();
	
	echo '<table border=0 width="100%"><tr><td valign="top">';
	
	echo '<form action="'. THIS_SCRIPT.'" method="post">
	    <input type="hidden" name="action" value="'.$_REQUEST['action'].'" />
	    <input type="hidden" name="grant" value="w" />
		<input type="hidden" name="promo_id" value="'.$fields['promo_id'].'" />
	
	    <div class="req">Required Field</div><br />
	
	    <fieldset>
	        <legend>Contact Information</legend>
			<div class="lbl req">Promotion ID</div><div class="data">' . $fields['promo_id'] . '<br><br></div>
			<div class="lbl req">Name</div><div class="data"><input type="text" name="name" value="'.$fields['name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Discount</div><div class="data"><input type="text" name="discount" value="'.$fields['discount'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Active</div><div class="data"><select name="is_active">'.getFormOptions($activeDrop, $fields['is_active']).'</select><br><br></div>
		</fieldset>
	
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo '<h1>List Promotions</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Add Promotion</a>  <br /><br />';
    echo '<table class="list">';
    echo '<tr>
    	<th>Promotion ID</th>
    	<th>Name</th>
    	<th>Discount</th>
    	<th>Active</th>
    	<th>Actions</th>
    	</tr>';

    if ($promos)
	{
		foreach ($promos as $k => $p)
		{
			$rowClass = $k % 2 ? 'row0' : 'row1';
			
			echo '<tr class="' . $rowClass . '">';
			echo '<td class="alt1">' . $p['promo_id'] . '</td>';
			echo '<td class="alt2">' . $p['name'] . '</td>';
			echo '<td class="alt1">' . $p['discount'] . '</td>';
			echo '<td class="alt2">' . boolToYesNo($p['is_active']) . '</td>';
			echo '<td class="alt1"><a href="' . THIS_SCRIPT . '?action=edit&promo_id=' . $p['promo_id'] . '">Edit Promotion</a> </td>';
			echo '</tr>';
		}
	}
	else
	{
	echo '<tr><td colspan="5">No promotions found.</td></tr>';
	}

    echo '</table>';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>