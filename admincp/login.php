<?php


$error = '';

if (!empty($_SERVER['HTTP_VIA']) || !empty($_SERVER['HTTP_FORWARDED']) || !empty($_SERVER['HTTP_FORWARDED_FOR']) || !empty($_SERVER['HTTP_FROM']))
{
    die('<b>You can not access this system while using a proxy.</b>');
}

session_start();


$server_ip = explode('.', $_SERVER['LOCAL_ADDR']);

if (empty($_SESSION['login_password_key']))
{
    $_SESSION['login_password_key'] =  strval(rand()) . strval(time());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['btnSubmit'])
{
	
	define('PATH_INCLUDE', $_SERVER['DOCUMENT_ROOT'] . '/includes/');
	define('PATH_CLASS', PATH_INCLUDE . 'classes/');
	define('ADMIN_PATH_INCLUDE', $_SERVER['DOCUMENT_ROOT'] . '/admincp/includes/');
	define('ADMIN_PATH_CLASS', ADMIN_PATH_INCLUDE . 'classes/');
	
	include_once (PATH_CLASS.'Database.class.php');
	$db	= new Database();
	
    define('THIS_SCRIPT', $_SERVER['PHP_SELF']);
    define('TIME_NOW', time());
	
    #require_once(PATH_INCLUDE . 'init.inc.php');
    require_once(PATH_CLASS . 'Error.class.php');
    
    require_once(ADMIN_PATH_INCLUDE . 'classes/AdminFunction.class.php');
    require_once(ADMIN_PATH_INCLUDE . 'classes/AdminAudit.class.php');
	
    $sql = "
        SELECT aa.admin_id AS id, a.admin_id, u.first_name, u.username, u.user_id
        FROM area_admin AS aa
            INNER JOIN admin AS a ON a.admin_id = aa.admin_id
            INNER JOIN users as u on u.user_id = a.user_id AND u.username = %s AND u.password = %s
        WHERE a.is_active = 1
            
    ";
	$admin = $db->safeReadQueryFirst($sql, $_POST['username'], md5($_POST['passwd_' . $_SESSION['login_password_key']]));
    
    if ($admin)
    {
    	
    	$sql = "SELECT a.area_id as id, COALESCE(a.area_name,'Global (No-Area)') AS title, a.area_name, a.is_active
		FROM area_admin as aa
		LEFT JOIN areas as a on a.area_id=aa.area_id
		WHERE aa.admin_id = %d 
			AND (aa.area_id = 0 OR a.is_active=1)
		ORDER BY a.area_name
		";
		
		$areas = $db->safeReadQueryAll($sql, $admin['id']);
		
        if ($admin && $areas)
        {
        	
            // save for login form
           setcookie('username', $_POST['username'], time() + (60*60*24), '/');

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['username'] = $admin['username'];
			$_SESSION['first_name'] = $admin['first_name'];
            $_SESSION['areas'] = $areas;
		
            $_SESSION['area_id'] = (int)$areas[0]['id'];
            $_SESSION['area_title'] = $areas[0]['title'];
            
            AdminAudit::log('LOGIN');
			
            header('Location: index.php');
            
            exit;
        }
        else
        {
            $error = "<tr><td colspan=\"2\"><font color=\"#cc0000\"><b>Your access level has not been configured.</b></font></td></tr>";
        }
    }
    else
    {
        setcookie('username', '', time() - (60*60*24), '/');

        AdminAudit::log('LOGIN', '', 'Failed Login');

        sleep(3);
        $error = "<tr><td colspan=\"2\"><font color=\"#cc0000\"><b>Incorrect username or password.</b></font></td></tr>";
		
        //generate new key
        $_SESSION['login_password_key'] =  strval(rand()) . strval(time());
    }
}
?>

<html>
<head>
    <title>Control Panel</title>
    <link type="text/css" rel="stylesheet" href="css/arenacp.css" />
	<style type="text/css">
		.main {
		  border:2px solid #666666;
		  background-color:#eeeeee;
		}
		.main th {
		  font-family:Tahoma,Arial,Verdana;
		  font-size:11px;
		  color:#000000;
		  border-bottom:2px solid #666666;
		}
		.main td {
		  font-family:Tahoma,Arial,Verdana;
		  font-size:11px;
		  color:#000000;
		}
		.main a:link, a:visited, a:active {
		  color:#333333;
		  text-decoration:none;
		}
		.main a:hover {
		  color:#333333;
		  text-decoration:underline;
		}
	</style>
    <script type="text/javascript">
        if (self.parent.frames.length != 0) self.parent.location.replace(document.location.href);
    </script>
</head>

<body bgcolor="#cccccc">
  <br />
  <br />
  <?php 
  
  
  ?>
  
  <br />
  <br />
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table border="0" cellpadding="5" cellspacing="0" align="center" class="main">
      <tr>
        <th colspan="2">Login</th>
      </tr>
      <tr>
        <td colspan="2" style="padding:0px; border-bottom:1px solid #666666; background-color:#ffffff;">
          <table border="0" cellpadding="0" cellspacing="0" height="70">
            <tr>
              <td width="195" height="120" bgcolor="0F1C46"> &nbsp; </td>
              <td width="30" bgcolor="0F1C46">&nbsp;</td>
              <td width="195" bgcolor="0F1C46">
                <font color="DDDDDD">Control Panel v1.0</font>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <?php print($error); ?>
      <tr>
        <td width="80">Username:</td>
        <td><input type="text" name="username" style="width:200px;" value="<?php echo $_COOKIE['username']; ?>" /></td>
      </tr>
      <tr>
        <td width="80">Password:</td>
        <td><input type="password" autocomplete="off" name="passwd_<?php echo $_SESSION['login_password_key']; ?>" style="width:200px;" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="Login" name="btnSubmit" style="width:80px;" /></td>
      </tr>
    </table>
  </form>
</body>
</html>
