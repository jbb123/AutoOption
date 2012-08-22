<?php

require_once ('includes/init.inc.php');

$reload_main = '';
// Reload arena dropdown list
$sql = "SELECT a.area_id as id, COALESCE(a.area_name,'Global (No-Area)') AS title, a.area_name, a.is_active
		FROM area_admin as aa
		LEFT JOIN areas as a on a.area_id=aa.area_id
		WHERE aa.admin_id = %d 
			AND (aa.area_id = 0 OR a.is_active=1)
		ORDER BY a.area_name
		";

$_SESSION['areas'] = $db->safeReadQueryAll($sql, $_SESSION['admin_id']);

// End Reload arena dropdown list

function setCurrentArea($area_id)
{
    global $db, $reload_main;
    
    $sql = "SELECT a.area_id as id, COALESCE(a.area_name,'Global (No-Area)') AS title, a.is_active
		FROM area_admin as aa
		LEFT JOIN areas as a on a.area_id=aa.area_id
		WHERE aa.area_id = %d 
			AND aa.admin_id = %d
		";
    $area = $db->safeReadQueryFirst($sql, $area_id, $_SESSION['admin_id']);
   
    $_SESSION['area_id'] = (int)$area['id'];
    $_SESSION['area_title'] = $area['title'];
    $reload_main = 'onload="parent.frame_main.document.location.href=\'./home.php\';';
    $reload_main .= 'parent.frame_head.document.location.href=\'./head.php\';"';
}

if (count($_SESSION['areas']) > 1 && isset($_POST['area_id']))
{
    setCurrentArea($_POST['area_id']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Management</title>
    <base target="frame_main" />
    <style type="text/css">
		@import url("css/admincp.css");
        body {
            color: #e1e1e1;
        }
		table.menu a.expand {
			background: url(images/common/icons/expand.png) no-repeat 0 0;
			padding: 0px 16px 0 0;
			margin: 0;
		}
		table.menu a.collapse {
			background: url(images/icons/collapse.png) no-repeat 0 0;
			padding: 0px 16px 0 0;
			margin: 0;
		}
    </style>
    
<script language="javascript" type="text/javascript">
  function toggleMenu(obj,e_id) {
	if (obj.className == 'collapse') {
		obj.className = 'expand';
		document.getElementById(e_id).style.display='none';
		//document.getElementById('arena-header').style.height='25px';
		//document.getElementById('arena-menu').style.top='0px';
		CCookie.set(e_id, 1, 7);
	}
	else {
		obj.className = 'collapse';
		document.getElementById(e_id).style.display='block';
		//document.getElementById('arena-header').style.height='105px';
		//document.getElementById('arena-menu').style.top='80px';
		CCookie.remove(e_id);
	}
}
</script>
</head>
<body bgcolor="2B2B3D" style="padding:0px; margin:0px;" <?php echo $reload_main; ?>>
  <div align="center">
    <br />

    <?php
    
    if (count($_SESSION['areas']) > 1)
    {
        echo "<style type=\"text/css\">optgroup,option {font-size:11px;}</style>";

        echo "<form action=\"./menu.php\" method=\"POST\" target=\"frame_nav\" style=\"margin:0px;\">";
        echo "<select name=\"area_id\" onchange=\"this.form.submit();\" style=\"width:175px;\">";

        
        foreach ($_SESSION['areas'] as $area)
        {
         	$selected = $area['id'] == $_SESSION['area_id'] ? ' selected' : '';
            echo '<option value="' . $area['id'] . '"' . $selected . '>' . $area['title'] . ' ('.$area['id'].')</option>';
		}

        echo '</select></form>';
    }
    else
    {
        echo '<b>' . $_SESSION['area_name'] . '</b><br />';
    }
    ?>
    <strong>Administration Menu</strong>
  </div>
  <br />

  <?php
  
  
  $menu = '';
  $last_group = '';
  $i = 1;
  
  $fn_id_list = AdminFunction::getFunctionIdList($_SESSION['area_id'], $_SESSION['admin_id']);
  
  $fn_mask_list_en = AdminFunction::getMaskedFunctionIdList($_SESSION['area_id'], $_SESSION['admin_id'], 'enabled');
  $fn_mask_list_dis = AdminFunction::getMaskedFunctionIdList($_SESSION['area_id'], $_SESSION['admin_id'], 'disabled');
  
  if(!empty($fn_mask_list_en))
  {
  	$fn_id_list .= ',';
  	$fn_id_list .= $fn_mask_list_en;
  }
  
  $where_function_ids = "AND af.admin_fn_id IN ($fn_id_list)";
  $where_function_dis = !empty($fn_mask_list_dis) ? "AND af.admin_fn_id NOT IN ($fn_mask_list_dis)" : '';
  
  switch($_SESSION['area_id'])
  {
    case 0:
        $where_class = "AND af.class IN ('global','site')";
    break;
  
    default:
        $where_class = "AND af.class IN ('site','area')";
    break;
  }
  
  $sql = "
    SELECT afg.title AS `group`, af.name AS `function`, af.script_name
    FROM admin_fn AS af
        INNER JOIN admin_fn_group AS afg ON afg.sequence > 0 AND afg.admin_fn_group_id = af.admin_fn_group_id
    WHERE af.enabled = 1
      AND af.show = 1
      $where_class
      $where_function_ids
	  $where_function_dis
    ORDER BY afg.sequence, af.sequence
  ";
  $rowSet = $db->safeReadQueryAll($sql);
  #$rowSet = $db->safeQueryDebug($sql);
  
  
  foreach ($rowSet as $row)
  {
  	$current = strtolower(str_replace(' ', '',$row['group']));
	$menu_header_hide = isset($_COOKIE[$current]); // || defined('FORCE_REQUEST_PATH');
	$menu_header_class = $menu_header_hide ? 'expand' : 'collapse';
    if ($last_group != $row['group'])
    {
      if ($last_group != '')
      {
        $menu .= "</table></div><br /><table class=\"menu\" align=\"center\" width=\"175\">\n";
      }
      $menu .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"175\" align=\"center\" class=\"menu\">\n";
	  $menu .= "<tr><th style=\"width: 152px;\"><font color=\"FFFFFF\">{$row['group']}</font></th><th align=\"right\"><a id=\"arenahdr-toggle\" class=\"$menu_header_class\" onclick=\"toggleMenu(this,'{$current}');\"></a>";
	  $menu .= "</th></tr></table><div id=\"{$current}\"";
	  if($menu_header_class == "expand")
	  {
	  $menu .= "style=\"display: none;\"";
	  }
	  $menu .= " ><table class=\"menu\" align=\"center\" width=\"175\" cellpadding=\"1\" cellspacing=\"0\">\n";

      $last_group = $row['group'];
    }

    $menu .= "<tr><td colspan=\"2\"><a href=\"{$row['script_name']}\">{$row['function']}</a></td></tr>
			  \n";
    $i++;

  }

  if ($menu != '') {
    $menu .= "</table></div>";
    print($menu);
  }
  ?>

  <img src="/images/table_row_bg_over.gif" width="0" height="0" border="1" style="visibility:hidden;" />
</body>
</html>

<?php
include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>
