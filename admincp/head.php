<?php
session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminCP Header</title>
    <style type="text/css">
		@import url("css/admincp.css");
        body {
            color: #e1e1e1;
        }
    </style>
</head>

<body id="header">
	<img src="images/logo.png" alt="Control Panel" hspace="4" vspace="4"><br>
    &nbsp;<a href="./" target="_top"><strong>Management Control Panel v.1.1</strong></a> |
	&nbsp;<a href="./logout.php" onClick="return confirm('Are you sure you want to log out of the control panel?');" target="_top"><strong>Log out</strong></a>
</body>
</html>
