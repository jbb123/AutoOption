<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('PACKAGE_LIST');

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
    	if (empty($_POST['name'])) Error::raise("You must provide a package name.");
    }
    
    if (Error::hasErrors())
    {
        return false;
    }
 	
    
    switch ($_REQUEST['action'])
    {
        case 'create':
            
            $sql = "
                INSERT INTO packages (category, name, description, price, listing_qty_limit, listing_time_limit, is_active)
                VALUES (%s, %s, %s, %f, %d, %d, %d)
            ";
            $db->safeQuery($sql,
            				$_POST['category'],
            				$_POST['name'],
            				$_POST['description'],
						    $_POST['price'],
						    $_POST['listing_qty_limit'],
						    $_POST['listing_time_limit'],
						    $_POST['is_active']
						    );
            $package_id= (int)$db->insertId();
			
			AdminAudit::log('PACKAGE_ADD',$package_id);
		break;
		
        case 'edit':
        	$sql = "
                UPDATE packages SET
                    category = %s,
					name = %s,
					description = %s,
                    price = %f,
					listing_qty_limit = %d,
					listing_time_limit = %d,
                    is_active = %d
				WHERE package_id = %d
            ";
            
            $db->safeQuery($sql,
               				$_POST['category'],
            				$_POST['name'],
            				$_POST['description'],
						    $_POST['price'],
						    $_POST['listing_qty_limit'],
						    $_POST['listing_time_limit'],
						    $_POST['is_active'],
						    $_POST['package_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('PACKAGE_UPDATE', $_POST['package_id']);
            break;
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'create':
      	$display = 'form';
      	$action_title = 'Create New Package';
      	if (save())
      	{
      		showManageRedirect('Package was successfully created.  Redirecting to Package list...', THIS_SCRIPT);
      	}
      break;

    case 'edit':
      	$display = 'form';
      	$action_title = 'Update Package';
      	if (save())
      	{
       		showManageRedirect('Package was successfully updated.  Redirecting to Package list...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

if ($display == 'form')
{
	
    if (isset($_REQUEST['package_id']))
    {
    	
        $sql = "
    		SELECT p.package_id, p.category, p.name, p.description, p.price, p.listing_qty_limit, p.listing_time_limit, p.is_active
			FROM packages as p
    		WHERE p.package_id = %d
		";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['package_id']);
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
    	SELECT p.package_id, p.category, p.name, p.description, p.price, p.listing_qty_limit, p.listing_time_limit, p.is_active
			FROM packages as p
    	ORDER BY p.name ASC
    ";
    
    $packages = $db->safeReadQueryAll($sql);
    
}

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

if ($display == 'form')
{
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

	$packageCats = array("dealer" => "dealer", "seller" => "seller");
	
	echo '<h1>'.$action_title .'</h1>';
	echo Error::getErrorList();
	
	echo '<table border=0 width="100%"><tr><td valign="top">';
	
	echo '<form action="'. THIS_SCRIPT.'" method="post">
	    <input type="hidden" name="action" value="'.$_REQUEST['action'].'" />
	    <input type="hidden" name="grant" value="w" />
		<input type="hidden" name="package_id" value="'.$fields['package_id'].'" />
	
	    <div class="req">Required Field</div><br />
	
	    <fieldset>
	        <legend>Package Information</legend>
			<div class="lbl req">Package ID</div><div class="data">' . $fields['package_id'] . '<br><br></div>
			<div class="lbl req">Type</div><div class="data"><select name="category">'.getFormOptions($packageCats, $fields['category']).'</select><br><br></div>
			<div class="lbl req">Name</div><div class="data"><input type="text" name="name" value="'.$fields['name'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Price</div><div class="data"><input type="text" name="price" value="'.$fields['price'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">How Many Listings</div><div class="data"><input type="text" name="listing_qty_limit" value="'.$fields['listing_qty_limit'].'" maxlength="50" class="lg"><div class="desc">0 = unlimited</div><br><br></div>
			<div class="lbl req">Listing Days Active</div><div class="data"><input type="text" name="listing_time_limit" value="'.$fields['listing_time_limit'].'" maxlength="50" class="lg"><div class="desc">0 = unlimited</div><br><br></div>
			<div class="lbl">Description</div><div class="data"><textarea name="description" style="width:650px; height:350px;">'. $fields['description'] . '</textarea></div>
			<div class="lbl req">Active</div><div class="data"><select name="is_active">'.getFormOptions($activeDrop, $fields['is_active']).'</select><br><br></div>
		</fieldset>
	
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo '<h1>List Packages</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Add Package</a>  <br /><br />';
    echo '<table class="list">';
    echo '<tr>
    	<th>Package ID</th>
    	<th>Type</th>
    	<th>Name</th>
    	<th>Price</th>
    	<th>How Many Listings</th>
    	<th>Listing Days Active</th>
    	<th>Active</th>
    	<th>Actions</th>
    	</tr>';

    if ($packages)
	{
		foreach ($packages as $k => $p)
		{
			$rowClass = $k % 2 ? 'row0' : 'row1';
			
			echo '<tr class="' . $rowClass . '">';
			echo '<td class="alt1">' . $p['package_id'] . '</td>';
			echo '<td class="alt2">' . $p['category'] . '</td>';
			echo '<td class="alt1">' . $p['name'] . '</td>';
			echo '<td class="alt2">' . $p['price'] . '</td>';
			echo '<td class="alt1">' . $p['listing_qty_limit'] . '</td>';
			echo '<td class="alt2">' . $p['listing_time_limit'] . '</td>';
			echo '<td class="alt1">' . boolToYesNo($p['is_active']) . '</td>';
			echo '<td class="alt2"><a href="' . THIS_SCRIPT . '?action=edit&package_id=' . $p['package_id'] . '">Edit Package</a> </td>';
			echo '</tr>';
		}
	}
	else
	{
	echo '<tr><td colspan="8">No packages found.</td></tr>';
	}

    echo '</table>';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>