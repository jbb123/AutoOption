<?php

include_once('includes/init.inc.php');

require_once(PATH_CLASS . 'Ftp.class.php');

$ftp = new FTP();

$fileList = $ftp->getFileList();

foreach ($fileList as $f)
{
	$data = $ftp->getFileContent($f);
	
	$ftp->import($data);
	
	$ftp->deleteFile($f);
	
}

?>



