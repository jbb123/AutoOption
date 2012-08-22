<?php

include_once (PATH_CLASS.'Dealer.class.php');
include_once (PATH_CLASS.'Authorize.class.php');

$dealer = New Dealer();
$packages = $dealer->getPackages();

$authorize = New Authorize();

$action = $path_split[3];

if ($action == "purchase" && $_SESSION['user_id'] && $_POST['grant'] == 'w')
{
	
	$package = $dealer->getPackage($_POST['package']);
	
	$expDate = $_POST['exp_mon'] . $_POST['exp_year'];
	
	$name = explode(" ", $_POST['name']);
	$firstName = $name[0];
	
	$lastName = $name[count($name) -1];
	$description = $package['name'] . ' (' . $package['package_id'] . ') Purchase';
	
	$authResult = $authorize->debit($_POST['cardnumber'], $expDate, $package['price'], $firstName, $lastName, $_POST['address'], $_POST['state'], $_POST['zip'], $description);
	
	if ($authResult[0] == 1)
	{
		echo 'Success!<br>';
		$successAdminAccount = $dealer->createDealerAccount();
		header('Location: /m/account/dashboard/');	
	}
	else
	{
		$errorTxt = "The transaction was declined. Please try again. (".$authResult[3].")";
	}
}

$pkgArr = array();
foreach ($packages as $p)
{
	$pkgArr[$p['package_id']] = $p['name'] . ' ($'.number_format($p['price'],2).')';
}
$months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$expMonth = array();
$x = 0;
while ($x != 12)
{
	$x++;
	$expMonth[str_pad($x, 2, "0", STR_PAD_LEFT)] = $months[$x];
}
$x = date("Y");
$yearTo = date("Y") + 8;
while ($x != $yearTo)
{
	$expYear[number_format($x, 0, '', '')] = number_format($x, 0, '', '');
	$x++;
}


?>

<?php

foreach ($packages as $p)
{
?>
<div class="search_form" style="float: left;">	
<form>
<dl>
	<dd>
	<h2><?php echo $p['name']; ?> </h2></dd>
	</dd>
	<dd><?php echo $p['description']; ?></dd>
</dl>
</form>
</div>
<?php	
}
?>

<?php
echo $errorTxt;
?>

<div class="search_form" style="float: left;">
	
	<form action="/c/packages/purchase" id="cardbform" method="post" accept-charset="utf-8">
	<input type="hidden" name="grant" value="w">					
		<dl>
			
			<dd><h2>Purchase Package</h2></dd>
			<dd><label>Package</label>
				<select name="package"><?php echo getFormOptions($pkgArr, 0); ?> </select>
			</dd>
			<dd><label>Billing Name</label>
				<input type="text" name="name" />
			</dd>
			<dd><label>Billing Address</label>
				<input type="text" name="address" />
			</dd>
			<dd><label>Billing City</label>
				<input type="text" name="city" />
			</dd>
			<dd><label>Billing State</label>
				<input type="text" name="state" />
			</dd>
			<dd><label>Billing zip</label>
				<input type="text" name="zip" />
			</dd>
			<dd><label>Credit Card Number</label>
				<input type="text" name="cardnumber" />
			</dd>
			<dd><label>Expiration Date</label>
				<select name ="exp_mon">
				<?php echo getFormOptions($expMonth, 0); ?>
				</select> / 
				<select name ="exp_year">
				<?php echo getFormOptions($expYear, date("Y")); ?>
				</select>
			</dd>
			<dd class="button">
				<input type="submit" name="action" value=" Purchase Package ">
			 </dd>
		</dl>
	</form>	
</div>







