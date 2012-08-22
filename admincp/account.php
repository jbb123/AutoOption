<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 'stdout');

include_once('includes/init.inc.php');
AdminFunction::requireAccess('ACCOUNT_INFO');

require_once(ADMIN_PATH_CLASS . 'Area.class.php');
include_once (PATH_CLASS.'Dealer.class.php');

$area = new Area();


$errors = array();

$areaInfo = $area->getInfo();
$dealer = New Dealer();

$dealerImages = $dealer->getDealerProfileImgs($areaInfo['area_id'], "full"); 

$dealerImg=array();
foreach ($dealerImages as $d)
{
	$dealerIMG[$d] = $d;
}



function save()
{
    global $db, $errors, $areaInfo;

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
					dealer_website = %s,
					dealer_update = %s,
					dealer_logo = %s,
					dealer_image = %s,
					profile_left = %s,
					profile_right = %s
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
				$_POST['dealer_update'],
				$_POST['dealer_logo'],
				$_POST['dealer_image'],
				$_POST['profile_left'],
				$_POST['profile_right'],
				$areaInfo['user_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('ACCOUNT_UPDATE',$areaInfo['user_id']);
			
			if (strlen(trim($_POST['passwd'])))
			{
				$sql = "UPDATE users
							SET password = MD5(%s)
						WHERE user_id = %d";
				$db->safeQuery($sql, $_POST['passwd'], $areaInfo['user_id']);
			}
			
			
            break;
        	
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    
    case 'edit':
      	$display = 'form';
      	$action_title = 'Update Account';
      	if (save())
      	{
       		showManageRedirect('Account was successfully updated.  Redirecting to Account Information ...', THIS_SCRIPT . '?action=edit');
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}
?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo SITE_URL; ?>/admincp/scripts/jquery.tools.min.1.2.6.js"></script>
<script type="text/javascript" src="http://static.gamebattles.com/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="http://static.gamebattles.com/javascript/ckeditor/adapters/jquery.js"></script>

<script language="javascript" type="text/javascript">
	jQuery(function() {
		jQuery('textarea').ckeditor({ toolbar : 'Full' });
	});
</script>

<?php


if ($display == 'form')
{
	
    if (isset($areaInfo['user_id']))
    {
    	
        $sql = "
    		SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website, u.dealer_update, u.dealer_logo, u.dealer_image, u.profile_right, u.profile_left
    		FROM users as u
    		WHERE u.user_id = %d
			ORDER BY u.username
    	";
        $fields = $db->safeReadQueryFirst($sql, $areaInfo['user_id']);
        
    }
    
    if (isset($_POST['action']))
    {
        $fields = array_merge((array)$fields, $_POST);
    }

    $fields = htmlEncode($fields);
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
			<div class="lbl req">Area ID</div><div class="data"> ' .$areaInfo['area_id'].' ('.$areaInfo['area_name'].') <br><br></div>
			<div class="lbl req">Dealership Name</div><div class="data"><input type="text" name="dealer_name" value="'.$fields['dealer_name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Address</div><div class="data"><input type="text" name="dealer_address" value="'.$fields['dealer_address'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">City</div><div class="data"><input type="text" name="dealer_city" value="'.$fields['dealer_city'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">State</div><div class="data"><input type="text" name="dealer_state" value="'.$fields['dealer_state'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Zipcode</div><div class="data"><input type="text" name="dealer_zip" value="'.$fields['dealer_zip'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Website</div><div class="data"><input type="text" name="dealer_website" value="'.$fields['dealer_website'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Status Update</div><div class="data"><textarea name="dealer_update" cols="75" rows="6">'.$fields['dealer_update'].'</textarea><br><br></div>
	    	<div class="lbl req">Dealer Logo</div><div class="data"> <select name="dealer_logo">'.getFormOptions($dealerIMG, $fields['dealer_logo']).'</select> </div>
			<div class="lbl req">Dealer Image</div><div class="data"> <select name="dealer_image">'.getFormOptions($dealerIMG, $fields['dealer_image']).'</select> </div>
			<div class="lbl">Profile Left</div><div class="data"><textarea name="profile_left" style="width:650px; height:350px;">'. $fields['profile_left'] . '</textarea></div>
			<div class="lbl">Profile Right</div><div class="data"><textarea name="profile_right" style="width:650px; height:350px;">'. $fields['profile_right'] . '</textarea></div>
		</fieldset>
			
			
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo 'Error: Profile Not Found.';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>