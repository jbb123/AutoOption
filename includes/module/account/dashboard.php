<?php

if (!$_SESSION['user_id'])
{
	$_SESSION['user_id'] = 0;
	$_SESSION['admin_id'] = 0;
	session_destroy();
	header('Location: /m/account/');
}


include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

$action = $path_split[3];
if ($action == "update" && $_POST['grant'] == 'w')
{
	$dealer->updateAccountBasic($_POST, $_SESSION['user_id']);
}

$userInfo = $dealer->getAccountBasic($_SESSION['user_id']);

$userSearches = $dealer->getUserSearches($_SESSION['user_id']);

?>




<div class="contents">
	<div class="inner_wrapper">
	
	
		<div class="search_form" style="float: left; margin: 40px 0 40px 0">
			<form action="/m/account/update" id="cardbform" method="post" accept-charset="utf-8">
				<input type="hidden" name="grant" value="w">					
				<dl>
					<dd><h2>Update My Account</h2></dd>
					<dd><label>Username</label>
						<?php echo $userInfo['username']; ?>
					</dd>
					
					<dd><label>First Name</label>
						<input type="text" name="first_name" value="<?php echo $userInfo['first_name']; ?>" />
					</dd>
					<dd><label>Last Name</label>
						<input type="text" name="last_name" value="<?php echo $userInfo['last_name']; ?>" />
					</dd>
					<dd><label>Email</label>
						<input type="text" name="email" value="<?php echo $userInfo['email']; ?>"/>
					</dd>
					
					<dd><label>Password</label>
						<input type="text" name="passwd" />
					</dd>
					<dd><label>Verify Password</label>
						<input type="text" name="passwd_confirm" />
					</dd>
					<dd>
						<input type="submit" name="action" value=" Update Account ">
					 </dd>
				</dl>
			</form>	
		</div>
		
		<div class="search_form" style="float: left; margin: 40px 0 40px 0">
		<?php 
		
		$hasAdminAccount = $dealer->hasAdminAccount($_SESSION['user_id']);
		
		if ($hasAdminAccount)
		{
		?>
			<h2>Launch Panel</h2>
			<a href="/admincp/">Manage my Listings</a>
			
		<?php
		}
		else
		{
		?>
			<h2>Upgrade Account</h2>
			<br>
			<dl>
			<dd>
			List your car on {site name} now. Check out out the packages we have to offer and sign up now.
			
			<br><br>
			
			Are you a dealer would you like to list your inventory on {site name}. Check out out the packages we have to offer and sign up now.
			</dd>
			</dl>
		
		<?php
		}
		?>
		
		
		
		</div>		
			
			
	<div class="search_form" style="float: left; margin: 40px 0 40px 0">
		<h2> Saved Searches </h2>
			<form action="/m/account/savesearch" id="cardbform" method="post" accept-charset="utf-8">
				<input type="hidden" name="grant" value="w">				
				<dl>
				<dd>
				<table border="0" cellspacing="0" cellpadding="0" style="margin: 0 0 0 35px" width="200">
				
				<?php
				if (count($userSearches))
				{
				?>
					<tr><th>Type</th><th> Name </th><th>Action</th></tr>
				<?php
				}
				?>
				<?php
				foreach ($userSearches as $s)
				{
				?>
				<tr>
					<td><?php echo $s['type']; ?></td>
					<td width="170"><input type="text" name="name[<?php echo $s['search_id'];?>]" value="<?php echo $s['name']; ?>" style="margin: 0 0 5px 0" /></td>
					<td>
					<?php
					if ($s['area_id'])
					{
						$dealerInfo = $dealer->getDealerByID($s['area_id']);
					?>	
						<a href="/m/dealers/<?php echo $dealerInfo['info']['area_name']; ?>/listings/?search_id=<?php echo $s['search_id']; ?>"><img src="/images/view_icon.png" width="16" height="16" alt="View Listing"></a>  | <a href="/m/account/removesearch/?search_id=<?php echo $s['search_id']; ?>"><img src="/images/delete-icon.png" width="16" height="16" alt="Delete Listing"></a>
					<?php
					}
					else if ($s['listing_id'])
					{
					?>	
						<a href="/l/<?php echo $s['listing_id']; ?>/"><img src="/images/view_icon.png" width="16" height="16" alt="View Listing"></a>  | <a href="/m/account/removesearch/?search_id=<?php echo $s['search_id']; ?>"><img src="/images/delete-icon.png" width="16" height="16" alt="Delete Listing"></a>
					<?php
					}
					else
					{
					?>	
						<a href="/m/search/results/?search_id=<?php echo $s['search_id']; ?>"><img src="/images/view_icon.png" width="16" height="16" alt="View Listing"></a>  | <a href="/m/account/removesearch/?search_id=<?php echo $s['search_id']; ?>"><img src="/images/delete-icon.png" width="16" height="16" alt="Delete Listing"></a>
					<?php
					}
					?>
					</td>
				</tr>
				<?php
				}
				?>
				</table>
				</dd>
				<?php
				if (count($userSearches))
				{
				?>
					<dd class="button">
						<input type="submit" name="sub_action" value="Update">
					</dd>
				<?php	
				}
				else
				{
				?>
				<dd> <br>No saved searches found. </dd>
				<?php
				}
				?>
				
				</dl>
			</form>	
		</div>
	
	<br><br>
	
	</div>
</div>