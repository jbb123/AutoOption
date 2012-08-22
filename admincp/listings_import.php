<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('LISTING_IMPORT');

$errors = array();

function save()
{
    global $db, $errors;

    $success = false;
    
    switch ($_REQUEST['action'])
    {
        case 'import':
            
            require_once(PATH_CLASS . 'Ftp.class.php');

			$ftp = new FTP();
			
			$fileList = $ftp->getFileList();
			
			foreach ($fileList as $f)
			{
				$data = $ftp->getFileContent($f);
				
				$ftp->import($data);
				
				$ftp->deleteFile($f);
				
			}
            
			AdminAudit::log('LISTING_IMPORT',0);
		break;
        
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'import':
      	
      	if (save())
      	{
      		showManageRedirect('Listing Import was successful.  Redirecting to Import...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

echo '<h1>Import Dealer Listings</h1>';
echo '<a href="' . THIS_SCRIPT . '?action=import">Run Import</a>  <br /><br />';


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>