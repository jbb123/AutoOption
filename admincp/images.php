<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('IMAGES_LIST');

$areaID = $_SESSION['area_id'];

include_once (PATH_CLASS.'Ftp.class.php');
include_once (PATH_CLASS.'ImgUploader.class.php');
include_once (PATH_CLASS.'Dealer.class.php');

function save()
{
	global $db, $areaID;
	
	$success = false;
    
    switch ($_REQUEST['action'])
    {
        case 'uploadIMG':
        	$filename = explode(".",$_FILES['file']['name']);
        	$filename = $filename[0];
        
        	$profilePath = '/users/'.$areaID.'/profile/';
    		$inventoryPath = '/users/'.$areaID.'/inventory/';
    		$filePath = ($_POST['image_type'] == "profile") ? $profilePath : $inventoryPath;
        	$img = new imgUploader($_FILES['file']);
            $time = TIME_NOW;
            $thumb = $img->upload($filePath, $filename.'_'.$time.'_thumb', 100,100);
			$full = $img->upload_unscaled($filePath, $filename.'_'.$time);
            
		break;
        
    }
    return true;
	
}


$dealer = New Dealer();
$profileImgs = $dealer->getDealerProfileImgs($areaID);
$inventoryImgs = $dealer->getDealerInventoryImgs($areaID);


switch ($_REQUEST['action'])
{
    case 'uploadIMG':
      	if (save())
      	{
      		showManageRedirect('Image was uploaded successfully.  Redirecting to Images...', THIS_SCRIPT.'?image_type='.$_POST['image_type']);
      	}
      break;
    
}


include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');
?>


<?php


$display = ($_REQUEST['image_type']) ? $_REQUEST['image_type'] : 'profile'; 

if ($display == "profile")
{
?>

	<h1>List Profile Images</h1>
	<h2><a href="<?php echo THIS_SCRIPT;?>?image_type=profile">Profile Images</a> | <a href="<?php echo THIS_SCRIPT;?>?image_type=inventory">Inventory Images</a> </h2>
	<table class="list" widht="100%">
	
	<tr>
		<?php
		$col = 0;
		$perRow = 3;
		foreach ($profileImgs as $p)
		{
			$col++;
		?>
		<td valign="top" align="center">
		<img src="/users/<?php echo $areaID;?>/profile/<?php echo $p;?>"><br>
		Delete
		
		</td>
		
		<?php
			if ($col==$perRow)
			{
				$col=0;
			?>
			</tr><tr>
			<?php
			}	
		}
		
		while ($col != $perRow)
		{
			$col++;
		?>
		<td></td>
		<?php	
		}
		
		?>
	</tr>
	
	<tr><td colspan="<?php echo $perRow; ?>">
	
		<form action="images.php" method="POST" enctype="multipart/form-data">
			<p>
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
				<input type="hidden" name="action" value="uploadIMG">
				<input type="hidden" name="image_type" value="profile">
				<input type="file" name="file" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="submit" value="Upload File" />
			</p>
		</form>
	
	</td></tr>
	
	</table>
<?php
}


if ($display == "inventory")
{
?>

	<h1>List Inventory Images</h1>
	<h2><a href="<?php echo THIS_SCRIPT;?>?image_type=profile">Profile Images</a> | <a href="<?php echo THIS_SCRIPT;?>?image_type=inventory">Inventory Images</a> </h2>
	<table class="list" widht="100%">
	
	<tr>
		<?php
		$col = 0;
		$perRow = 3;
		foreach ($inventoryImgs as $p)
		{
			$col++;
		?>
		<td valign="top" align="center">
		<img src="/users/<?php echo $areaID;?>/inventory/<?php echo $p;?>"><br>
		Delete
		
		</td>
		
		<?php
			if ($col==$perRow)
			{
				$col=0;
			?>
			</tr><tr>
			<?php
			}	
		}
		
		while ($col != $perRow)
		{
			$col++;
		?>
		<td></td>
		<?php	
		}
		
		?>
	</tr>
	
	<tr><td colspan="<?php echo $perRow; ?>">
	
		<form action="images.php" method="POST" enctype="multipart/form-data">
			<p>
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
				<input type="hidden" name="action" value="uploadIMG">
				<input type="hidden" name="image_type" value="inventory">
				<input type="file" name="file" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="submit" value="Upload File" />
			</p>
		</form>
	
	</td></tr>
	
	</table>
<?php
}




include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>





