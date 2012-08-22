<?php

$subPage = $path_split[3];

if ($subPage == "update")
{
	$subPage = "dashboard";
}

SWITCH ($subPage)
{
	case 'login':
	case 'logout':
	case 'dashboard':
	case 'update':
	case 'savesearch':
	case 'removesearch':
	case 'createaccount':
		require_once($subPage . '.php');
		include_once('templates/footer.inc.php');
		die;
		break;
}


if ($_SESSION['user_id'])
{
	header('Location: /m/account/dashboard/');
}

?>


<div class="contents">
	<div class="inner_wrapper">
<?php
if ($_REQUEST['login_invalid'])
{
	echo '<br><br>';
	echo '<center>Invalid Login or Password. Please try again or use our forgot password to retrieve your account info.</center>';
	
}
?>
<table border="0" cellspacing="0" cellpadding="0" align="center" width="700">

	<tr>
		<td width="50%">
		
		<div class="search_form">
			<form action="/m/account/login" id="cardbform" method="post" accept-charset="utf-8">					
				<dl>
					<dd><h2>Login</h2></dd>
					<dd><label>Username</label>
						<input type="text" name="username" />
					</dd>
					<dd><label>Password</label>
						<input type="text" name="passwd" />
					</dd>
					<dd class="button">
						<input type="submit" name="action" value=" Login ">
					 </dd>
				</dl>
			</form>	
		</div>
		
		
		</td>
		
		<td width="50%">
		
		<div class="search_form">	
			<form action="/m/account/createaccount" id="cardbform" method="post" accept-charset="utf-8">
			<input type="hidden" name="grant" value="w">					
				<dl>
					<dd><h2>Create an Account</h2></dd>
					<dd><label>First Name</label>
						<input type="text" name="first_name" />
					</dd>
					<dd><label>Last Name</label>
						<input type="text" name="last_name" />
					</dd>
					<dd><label>Email</label>
						<input type="text" name="email" />
					</dd>
					<dd><label>Username</label>
						<input type="text" name="username" />
					</dd>
					<dd><label>Password</label>
						<input type="text" name="passwd" />
					</dd>
					<dd class="button">
						<input type="submit" name="action" value=" Create Account ">
					 </dd>
				</dl>
			</form>	
		</div>
		
		</td>
	</tr>


</table>
	
	<br><br>
	
	</div>
</div>



