<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('CONTENT_LIST');

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
    	if (empty($_POST['page_title'])) Error::raise("You must provide a page title.");
    }
    
    if (Error::hasErrors())
    {
        return false;
    }
 	
    
    switch ($_REQUEST['action'])
    {
        case 'create':
            
            $sql = "
                INSERT INTO content (page_title, content, sequence, is_active)
                VALUES (%s, %s, %d, %d)
            ";
            $db->safeQuery($sql,
            				$_POST['page_title'],
						    $_POST['content'],
						    $_POST['sequence'],
						    $_POST['is_active']
						    );
            $content_id= (int)$db->insertId();
			
			AdminAudit::log('CONTENT_ADD',$content_id);
		break;
		
        case 'edit':
        	$sql = "
                UPDATE content SET
                    page_title = %s,
                    content = %s,
                    sequence = %d,
                    is_active = %d
                WHERE content_id = %d
            ";
            
            $db->safeQuery($sql,
                $_POST['page_title'],
                $_POST['content'],
                $_POST['sequence'],
                $_POST['is_active'],
				$_POST['content_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('CONTENT_UPDATE',$_POST['content_id']);
            break;
        	
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'create':
      	$display = 'form';
      	$action_title = 'Create New Page';
      	if (save())
      	{
      		showManageRedirect('Page was successfully created.  Redirecting to Content list...', THIS_SCRIPT);
      	}
      break;

    case 'edit':
      	$display = 'form';
      	$action_title = 'Update Page';
      	if (save())
      	{
       		showManageRedirect('Page was successfully updated.  Redirecting to Content list...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

if ($display == 'form')
{
	
    if (isset($_REQUEST['content_id']))
    {
    	
        $sql = "
    		SELECT c.content_id, c.page_title, c.content, c.sequence, c.is_active
			FROM content as c
    		WHERE c.content_id = %d
		";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['content_id']);
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
    	SELECT c.content_id, c.page_title, c.content, c.sequence, c.is_active
    	FROM content as c
    	ORDER BY c.sequence
    ";
    
    $content = $db->safeReadQueryAll($sql);
    
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
	
	
	echo '<h1>'.$action_title .'</h1>';
	echo Error::getErrorList();
	
	echo '<table border=0 width="100%"><tr><td valign="top">';
	
	echo '<form action="'. THIS_SCRIPT.'" method="post">
	    <input type="hidden" name="action" value="'.$_REQUEST['action'].'" />
	    <input type="hidden" name="grant" value="w" />
		<input type="hidden" name="content_id" value="'.$fields['content_id'].'" />
	
	    <div class="req">Required Field</div><br />
	
	    <fieldset>
	        <legend>Content Information</legend>
			<div class="lbl req">Content ID</div><div class="data">' . $fields['content_id'] . '<br><br></div>
			<div class="lbl req">Page Title</div><div class="data"><input type="text" name="page_title" value="'.$fields['page_title'].'" maxlength="50" class="lg"><br><br></div>
			
			<div class="lbl">Content</div><div class="data"><textarea name="content" style="width:650px; height:350px;">'. $fields['content'] . '</textarea></div>
			
			
			
			<div class="lbl req">Sequence</div><div class="data"><input type="text" name="sequence" value="'.$fields['sequence'].'" maxlength="2" class="lg"><br><br></div>
			<div class="lbl req">Active</div><div class="data"><select name="is_active">'.getFormOptions($activeDrop, $fields['is_active']).'</select><br><br></div>
		</fieldset>
	
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo '<h1>List Pages</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Add Page</a>  <br /><br />';
    echo '<table class="list">';
    echo '<tr>
    	<th>Content ID</th>
    	<th>Page Title</th>
    	<th>Sequence</th>
    	<th>Active</th>
    	<th>Actions</th>
    	</tr>';

    if ($content)
	{
		foreach ($content as $k => $c)
		{
			$rowClass = $k % 2 ? 'row0' : 'row1';
			
			echo '<tr class="' . $rowClass . '">';
			echo '<td class="alt1">' . $c['content_id'] . '</td>';
			echo '<td class="alt2">' . $c['page_title'] . '</td>';
			echo '<td class="alt1">' . $c['sequence'] . '</td>';
			echo '<td class="alt2">' . boolToYesNo($c['is_active']) . '</td>';
			echo '<td class="alt1"><a href="' . THIS_SCRIPT . '?action=edit&content_id=' . $c['content_id'] . '">Edit Page</a> </td>';
			echo '</tr>';
		}
	}
	else
	{
	echo '<tr><td colspan="6">No content found.</td></tr>';
	}

    echo '</table>';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>