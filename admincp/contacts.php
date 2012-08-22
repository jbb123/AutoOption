<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('CONTACT_LIST');

$errors = array();

function save()
{
    global $db, $errors;

    $success = false;

    if ($_POST['grant'] != 'w')
    {
        return false;
    }
    
    $sqlContactWhere = ($_SESSION['area_id']) ? ' AND c.area_id = ' . $_SESSION['area_id'] : '';
    
    if (in_array($_REQUEST['action'], array('create', 'edit')))
    {
    	if (empty($_POST['name'])) Error::raise("You must provide a contact name.");
    }
    
    if (Error::hasErrors())
    {
        return false;
    }
 	
    
    switch ($_REQUEST['action'])
    {
        case 'create':
            
            $sql = "
                INSERT INTO contacts (area_id, name, email, phone, comment, create_time, ip_address)
                VALUES (%d, %s, %s, %s, %s, %d, %d)
            ";
            $db->safeQuery($sql,
            				$_SESSION['area_id'],
            				$_POST['name'],
						    $_POST['email'],
						    $_POST['phone'],
						    $_POST['comment'],
						    TIME_NOW,
						    ip2Long($_SERVER['REMOTE_ADDR'])
						    );
            $contact_id= (int)$db->insertId();
			
			AdminAudit::log('CONTACT_ADD',$contact_id);
		break;
		
        case 'edit':
        	$sql = "
                UPDATE contacts SET
                    name = %s,
                    email = %s,
                    phone = %s,
					comment = %s
                WHERE contact_id = %d {$sqlContactWhere}
            ";
            
            $db->safeQuery($sql,
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['comment'],
                $_POST['contact_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('CONTACT_UPDATE',$_POST['contact_id']);
            break;
        	
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'create':
      	$display = 'form';
      	$action_title = 'Create New Contact';
      	if (save())
      	{
      		showManageRedirect('Contact was successfully created.  Redirecting to Contact list...', THIS_SCRIPT);
      	}
      break;

    case 'edit':
      	$display = 'form';
      	$action_title = 'Update Contact';
      	if (save())
      	{
       		showManageRedirect('Contact was successfully updated.  Redirecting to Contact list...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

$sqlContactWhere = ($_SESSION['area_id']) ? ' AND c.area_id = ' . $_SESSION['area_id'] : '';
if ($display == 'form')
{
	
    if (isset($_REQUEST['contact_id']))
    {
    	
        $sql = "
    		SELECT c.contact_id, c.name, c.email, c.phone, c.comment, c.create_time, c.ip_address
			FROM contacts as c
    		WHERE c.contact_id = %d {$sqlContactWhere}
		";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['contact_id']);
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
    	SELECT c.contact_id, c.area_id, c.name, c.email, c.phone, c.comment, c.create_time, c.ip_address,
				a.area_name
			FROM contacts as c
		LEFT JOIN areas as a on a.area_id = c.area_id
		WHERE 1 = 1 {$sqlContactWhere}
    	ORDER BY c.create_time DESC
    ";
    
    $contacts = $db->safeReadQueryAll($sql);
    
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
		<input type="hidden" name="contact_id" value="'.$fields['contact_id'].'" />
	
	    <div class="req">Required Field</div><br />
	
	    <fieldset>
	        <legend>Contact Information</legend>
			<div class="lbl req">Contact ID</div><div class="data">' . $fields['contact_id'] . '<br><br></div>
			<div class="lbl req">Create Time</div><div class="data">' . tsDisplay($fields['create_time']) . '<br><br></div>
			<div class="lbl req">IP Address</div><div class="data">' . long2ip($fields['ip_address']) . '<br><br></div>
			<div class="lbl req">Name</div><div class="data"><input type="text" name="name" value="'.$fields['name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Email</div><div class="data"><input type="text" name="email" value="'.$fields['email'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Phone</div><div class="data"><input type="text" name="phone" value="'.$fields['phone'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Comments</div><div class="data"><textarea name="comment" cols="50" rows="6">'.$fields['comment'] . '</textarea><br><br></div>
			
		</fieldset>
	
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo '<h1>List Contacts</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Add Contact</a>  <br /><br />';
    echo '<table class="list">';
    echo '<tr>
    	<th>Contact ID</th>
    	<th>Dealer</th>		
    	<th>Created</th>		
		<th>Name</th>
    	<th>Email</th>
    	<th>Phone</th>
    	<th>Actions</th>
    	</tr>';

    if ($contacts)
	{
		foreach ($contacts as $k => $c)
		{
			$areaName = ($c['area_name']) ? $c['area_name'] : 'Global';
			$areaName = str_replace("_", " ", $areaName);
			$rowClass = $k % 2 ? 'row0' : 'row1';
			
			echo '<tr class="' . $rowClass . '">';
			echo '<td class="alt1">' . $c['contact_id'] . '</td>';
			echo '<td class="alt2">' . $areaName . '</td>';
			echo '<td class="alt1">' . tsDisplay($c['create_time']) . '</td>';
			echo '<td class="alt2">' . $c['name'] . '</td>';
			echo '<td class="alt1">' . $c['email'] . '</td>';
			echo '<td class="alt2">' . $c['phone'] . '</td>';
			echo '<td class="alt1"><a href="' . THIS_SCRIPT . '?action=edit&contact_id=' . $c['contact_id'] . '">Edit Contact</a> </td>';
			echo '</tr>';
		}
	}
	else
	{
	echo '<tr><td colspan="7">No contacts found.</td></tr>';
	}

    echo '</table>';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>