<?php

require_once('includes/init.inc.php');

$main_src = './home.php';

if (!empty($_SESSION['redirect_url']))
{
  $main_src = $_SESSION['redirect_url'];
  unset($_SESSION['redirect_url']);
}
?>

<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <script type="text/javascript">
    if (self.parent.frames.length != 0) self.parent.location.replace(document.location.href);
  </script>
  <link rel="stylesheet" media="print" href="css/print.css" type="text/css" />
  <title>Control Panel</title>

  <frameset cols="195,*" framespacing="0" border="0" frameborder="0" frameborder="no" border="0">
    <frame src="./menu.php" name="frame_nav" id="frame_nav" scrolling="auto" frameborder="0" marginwidth="0" marginheight="0" border="no" />
    <frameset rows="110,*"  framespacing="0" border="0" frameborder="0" frameborder="no" border="0">
      <frame src="./head.php" name="frame_head" id="frame_head" scrolling="no" noresize="noresize" frameborder="0" marginwidth="10" marginheight="0" border="no" />
      <frame src="<?php echo $main_src; ?>" name="frame_main" id="frame_main" scrolling="yes" frameborder="0" marginwidth="10" marginheight="10" border="no" />
    </frameset>
  </frameset>

  <noframes>
    <body>
      <p>Your browser does not support frames. Please get one that does!</p>
    </body>
  </noframes>
</head>
</html>
