<?php
if (!ISSET($_SESSION['admin_id']))
{
  header("Location: ".SITE_URL."/admincp/login.php");
  exit;
}

function hasAccess($fn)
{
  return AdminFunction::hasAccess($fn);
}

function checkAccess($fn)
{
  return AdminFunction::requireAccess($fn);
}
?>
