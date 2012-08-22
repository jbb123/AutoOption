<?php

$dealerName = $path_split[3];
$subPage = $path_split[4];
if ($dealerName)
{
	if ($subPage=="listings")
	{
			require_once('listings.php');
			include_once('templates/footer.inc.php');
			die;
	}
	else if ($subPage=="contact")
	{
			require_once('contact.php');
			include_once('templates/footer.inc.php');
			die;
	}
	else
	{
		if ($dealerName != "index.php")
		{
			require_once('profile.php');
			include_once('templates/footer.inc.php');
			die;
		}	
	}
	
	
}


include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

if ($_POST['action'] == "search")
{
	$dealers = $dealer->getDealers($_POST['dealership'], $_POST['city'], $_POST['state']);
}

?>


<div class="contnets">
	<div class="inner_wrapper">




<form action="/m/dealers/index.php" method="post">
    <input type="hidden" name="action" value="search" />
	<input type="hidden" name="grant" value="w" />
	
	<fieldset>
		<legend>Dealer/Seller Search </legend>
		<div class="notes">
			<h4>Search Sellers</h4>
	        <p class="last">Please enter your name and address as they are listed for your debit card, credit card, or bank account.</p>
		</div>
									
		<div class="required">
			<label for="name">Dealership:</label>
			<input type="text" name="dealership" id="dealership" class="inputText" size="10" maxlength="100" value="<?php echo $_POST['dealership']; ?>" />
		</div>
		<div class="required">
			<label for="name">City:</label>
			<input type="text" name="city" id="city" class="inputText" size="10" maxlength="100" value="<?php echo $_POST['city']; ?>" />
		</div>
		<div class="required">
			<label for="name">State:</label>
			<input type="text" name="state" id="state" class="inputText" size="10" maxlength="100" value="<?php echo $_POST['state']; ?>" />
		</div>
		
	</fieldset>
    <fieldset>
      <div class="submit">
        <div>
          <input type="submit" class="inputSubmit" value="Search" />
          
        </div>
      </div>
    </fieldset>
</form>


<?php 
if ($dealers)
{
?>
<hr>

<?php	
	echo '<center><font color="00AA00">Search Results Below</font></center>';
?>

<table border="0" width="600" cellspacing="0" cellpadding="0">

<?php
foreach ($dealers as $d)
{
?>

<tr>
	<td valign="top">
	<?php
	if ($d['dealer_logo'])
	{
	?>
		<img src="/users/<?php echo $d['area_id']; ?>/profile/<?php echo $d['dealer_logo'];?>">
	<?php	
	}
	?>
	
	</td>
	<td valign="top">
	<?php echo str_replace("_", " ", $d['dealer_name']);?><br>
	<?php echo $d['dealer_address'] . ' ' . $d['city'] . ' ' . $d['state'];?><br>
	<a href="<?php echo $d['dealer_website'];?>" targer="_new"><?php echo $d['dealer_website'];?></a><br>
	<?php echo $d['dealer_phone'];?><br>
	</td>
	<td valign="top">
	<a href="<?php echo $d['dealer_name'] . '/listings'; ?>">View All Listings</a><br>
	<a href="<?php echo $d['dealer_name'] . '/contact'; ?>">Contact Agent</a><br>
	<a href="<?php echo $d['dealer_name']; ?>">View Full Profile</a><br>
	</td>
	
</tr>

<?php
}
?>

</table>


<?php	
}

?>





	
	</div>
</div>