<?php
require_once ('includes\header.inc.php');
?>
<style type="text/css">
   table.list tr.total td {background:#ffffe0 none; color:#a15000;}
table.list th {background:#FFEE99; color:#a15000;}
height:45px;}
</style>
<h2>Welcome to the <?php echo $_SESSION['merchant_title']; ?> administration console.</h2>

<?php



include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>