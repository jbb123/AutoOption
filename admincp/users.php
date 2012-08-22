<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('USER_LIST');

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
    	if (empty($_POST['username'])) Error::raise("You must provide a username.");
    }
    
    if (Error::hasErrors())
    {
        return false;
    }
 	
    
    switch ($_REQUEST['action'])
    {
        case 'create':
            $sql = "
                INSERT INTO users (username, first_name, last_name, email, email_visible, password, create_time, dealer_name, dealer_address, dealer_city, dealer_state, dealer_zip, dealer_website)
                VALUES (%s, %s, %s, %s, %d, %s, %d, %s, %s, %s, %s, %s, %s)
            ";
            $db->safeQuery($sql,
            				$_POST['username'],
						    $_POST['first_name'],
						    $_POST['last_name'],
						    $_POST['email'],
						    $_POST['email_visible'],
						    MD5($_POST['passwd']),
						    TIME_NOW,
						    $_POST['dealer_name'],
						    $_POST['dealer_address'],
						    $_POST['dealer_city'],
						    $_POST['dealer_state'],
						    $_POST['dealer_zip'],
						    $_POST['dealer_website']
						    );
            $user_id= (int)$db->insertId();
			
			AdminAudit::log('USER_CREATE',$user_id);
		break;
		
        case 'edit':
        	$sql = "
                UPDATE users SET
                    username = %s,
                    first_name = %s,
                    last_name = %s,
                    phone = %s,
					email = %s,
					email_visible = %d,
					is_active = %d,
					dealer_name = %s, 
					dealer_address = %s, 
					dealer_city = %s, 
					dealer_state = %s, 
					dealer_zip = %s, 
					dealer_website = %s
                WHERE user_id = %d
            ";
            
            $db->safeQuery($sql,
                $_POST['username'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['phone'],
                $_POST['email'],
                $_POST['email_visible'],
				$_POST['is_active'],
				$_POST['dealer_name'],
				$_POST['dealer_address'],
				$_POST['dealer_city'],
				$_POST['dealer_state'],
				$_POST['dealer_zip'],
				$_POST['dealer_website'],
				$_POST['user_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('USER_UPDATE',$_POST['user_id']);
			
			if (strlen(trim($_POST['passwd'])))
			{
				$sql = "UPDATE users
							SET password = MD5(%s)
						WHERE user_id = %d";
				$db->safeQuery($sql, $_POST['passwd'], $_POST['user_id']);
			}
			
			
            break;
        	
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'create':
      	$display = 'form';
      	$action_title = 'Create New User';
      	if (save())
      	{
      		showManageRedirect('User was successfully created.  Redirecting to User list...', THIS_SCRIPT);
      	}
      break;

    case 'edit':
      	$display = 'form';
      	$action_title = 'Update User';
      	if (save())
      	{
       		showManageRedirect('User was successfully updated.  Redirecting to User list...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

if ($display == 'form')
{
	
    if (isset($_REQUEST['user_id']))
    {
        $sql = "
    		SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website
    		FROM users as u
    		WHERE u.user_id = %d
			ORDER BY u.username
    	";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['user_id']);
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
    	SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website
    	FROM users as u
    	ORDER BY u.username
    ";
    
    $users = $db->safeReadQueryAll($sql);
    
}

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

if ($display == 'form')
{
	echo '<h1>'.$action_title .'</h1>';
	echo Error::getErrorList();
	
	echo '<table border=0 width="100%"><tr><td valign="top">';
	
	echo '<form action="'. THIS_SCRIPT.'" method="post">
	    <input type="hidden" name="action" value="'.$_REQUEST['action'].'" />
	    <input type="hidden" name="grant" value="w" />
		<input type="hidden" name="user_id" value="'.$fields['user_id'].'" />
	
	    <div class="req">Required Field</div><br />
	
	    <fieldset>
	        <legend>User Information</legend>
			<div class="lbl req">Create Time</div><div class="data">' . tsDisplay($fields['create_time']) . '<br><br></div>
			<div class="lbl req">Username</div><div class="data"><input type="text" name="username" value="'.$fields['username'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">First</div><div class="data"><input type="text" name="first_name" value="'.$fields['first_name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Last</div><div class="data"><input type="text" name="last_name" value="'.$fields['last_name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Phone</div><div class="data"><input type="text" name="phone" value="'.$fields['phone'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Email</div><div class="data"><input type="text" name="email" value="'.$fields['email'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Show Email</div><div class="data"><select name="email_visible">'.getFormOptions($yesNoDrop, $fields['email_visible']).'</select><br><br></div>
			<div class="lbl req">Password</div><div class="data"><input type="text" name="passwd" value="" maxlength="50" class="lg"><div class="desc">Only updates if entered.</div><br><br></div>
	        <div class="lbl req">Active</div><div class="data"><select name="is_active">'.getFormOptions($activeDrop, $fields['is_active']).'</select><br><br></div>
		</fieldset>
		
		<fieldset>
	        <legend>Dealer Information</legend>
			<div class="lbl req">Area ID</div><div class="data"> <br><br></div>
			<div class="lbl req">Dealership Name</div><div class="data"><input type="text" name="dealer_name" value="'.$fields['dealer_name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Address</div><div class="data"><input type="text" name="dealer_address" value="'.$fields['dealer_address'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">City</div><div class="data"><input type="text" name="dealer_city" value="'.$fields['dealer_city'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">State</div><div class="data"><input type="text" name="dealer_state" value="'.$fields['dealer_state'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Zipcode</div><div class="data"><input type="text" name="dealer_zip" value="'.$fields['dealer_zip'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Website</div><div class="data"><input type="text" name="dealer_website" value="'.$fields['dealer_website'].'" maxlength="50" class="lg"><br><br></div>
	        
		</fieldset>
			
			
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo '<h1>List Users</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Add User</a>  <br /><br />';
    echo '<table class="list">';
    echo '<tr>
    	<th>User ID</th>
    	<th>Username</th>
    	<th>Name</th>
    	<th>Email</th>
    	<th>Active</th>
    	<th>Actions</th>
    	</tr>';

    if ($users)
	{
		foreach ($users as $k => $u)
		{
			$rowClass = $k % 2 ? 'row0' : 'row1';
			
			echo '<tr class="' . $rowClass . '">';
			echo '<td class="alt1">' . $u['user_id'] . '</td>';
			echo '<td class="alt2">' . $u['username'] . '</td>';
			echo '<td class="alt1">' . $u['first_name'] . ' ' . $u['last_name'] . '</td>';
			echo '<td class="alt2">' . $u['email'] . '</td>';
			echo '<td class="alt1">' . boolToYesNo($u['is_active']) . '</td>';
			echo '<td class="alt2"><a href="' . THIS_SCRIPT . '?action=edit&user_id=' . $u['user_id'] . '">Edit User</a> </td>';
			echo '</tr>';
		}
	}
	else
	{
	echo '<tr><td colspan="6">No users found.</td></tr>';
	}

    echo '</table>';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>