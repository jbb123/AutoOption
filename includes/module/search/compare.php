<?php

include_once (PATH_CLASS.'Dealer.class.php');


if (ISSET($_REQUEST['r_vehicle']))
{
	$vin = $_REQUEST['r_vehicle'];
	$keyFind = array_search($vin, $_SESSION['compVehicles']);
	
	if ($keyFind === false)
	{}
	else
	{
		unset($_SESSION['compVehicles'][$keyFind]); 
	}
}

$dealer = New Dealer();

$listings = $dealer->getCompareListings($_SESSION['compVehicles']);

$vehicleTxt = "";
$vinTxt = "";
$mileageTxt = "";
$yearTxt = "";
$makeModelTxt = "";
$priceTxt = "";
$carTypeTxt = "";
$sellerCommentsTxt = "";
$bodyStyleTxt = "";
$addressTxt = "";
$extColorTxt = "";
$intColorTxt = "";
$doorsTxt = "";
$driveTypeTxt = "";
$driveAirBagTxt = "";
$engineTxt = "";
$transTxt = "";
$fuelTypeTxt = "";
$passengerAirBagTxt = "";
$sideAirBagTxt = "";
$antiLockBrakeTxt = "";
$leatherSeatTxt = "";
$airConditioningTxt = "";
$powerSteeringTxt = "";
$cruiseControlTxt = "";
$tiltWheelTxt = "";
$powerSeatTxt = "";
$childSeatTxt = "";
$powerWindowTxt = "";
$rearWindowDefrosterTxt = "";
$tintedGlassTxt = "";
$amFMRadioTxt = "";
$amFMStereoTapeTxt = "";
$cdPlayerTxt = "";
$cdChangerTxt = "";
$alloyWheelsTxt = "";
$powerDoorLocksTxt = "";
$powerMirrorTxt = "";
$sunroofTxt = "";
$gpsTxt = "";
$rearEntTxt = "";
$soldTxt = "";


foreach ($listings as $l)
{
	$listPhotos = explode(";",$l['pictures']);
	$mainPhoto = (strlen($listPhotos[0])) ? $listPhotos[0] : 0;
	
	$vehicleTxt .= "<td><img src=". $mainPhoto ." alt='Vehicle'  width='75' height='50''/></td>";
	$vinTxt .= "<td>" . $l['vin'] . "</td>";
	$mileageTxt = "<td>" . $l['mileage'] . "</td>";
	$yearTxt .= "<td>" . $l['year'] . "</td>";
	$makeModelTxt .= "<td>" . $l['make'] . ' ' . $l['model'] . "</td>";
	$priceTxt .= "<td>" . $l['price'] . "</td>";
	$carTypeTxt .= "<td>" . $l['car_type'] . "</td>";
	$sellerCommentsTxt .= "<td>" . $l['seller_comments']  . "</td>";
	$bodyStyleTxt .= "<td>" . $l['body_style'] . "</td>";
	$addressTxt .= "<td>" . $l['address'] . "<br>" . $l['city'] . ' ' . $l['state'] . ', ' . $l['zipcode'] . "</td>";
	$extColorTxt .= "<td>" . $l['exterior_color'] . "</td>";
	$intColorTxt .= "<td>" . $l['interior_color'] . "</td>";
	$doorsTxt .= "<td>" . boolToYesNo($l['doors']) . "</td>";
	$driveTypeTxt .= "<td> </td>";
	$driveAirBagTxt .= "<td>" . boolToYesNo($l['driver_air_bag']) . "</td>";
	$engineTxt .= "<td>" . $l['engine'] . "</td>";
	$transTxt .= "<td>" . $l['transmission'] . "</td>";
	$fuelTypeTxt .= "<td>" . $l['fuel_type'] . "</td>";
	$passengerAirBagTxt .= "<td>" . boolToYesNo($l['passenger_air_bag']) . "</td>";
	$sideAirBagTxt .= "<td> </td>";
	$antiLockBrakeTxt .= "<td>" . boolToYesNo($l['anti_lock_brakes']) . "</td>";
	$leatherSeatTxt .= "<td>" . boolToYesNo($l['leather_seats']) . "</td>";
	$airConditioningTxt .= "<td>" . boolToYesNo($l['air_conditioning']) . "</td>";
	$powerSteeringTxt .= "<td>" . boolToYesNo($l['power_steering']) . "</td>";
	$cruiseControlTxt .= "<td>" . boolToYesNo($l['cruise_control']) . "</td>";
	$tiltWheelTxt .= "<td>" . boolToYesNo($l['tilt_wheel']) . "</td>";
	$powerSeatTxt .= "<td>" . boolToYesNo($l['power_seats']) . "</td>";
	$childSeatTxt .= "<td>" . boolToYesNo($l['child_seat']) . "</td>";
	$powerWindowTxt .= "<td>" . boolToYesNo($l['power_window']) . "</td>";
	$rearWindowDefrosterTxt .= "<td>" . boolToYesNo($l['rear_window'])  . "</td>";
	$tintedGlassTxt .= "<td>" . boolToYesNo($l['tinted_glass'])  . "</td>";
	$amFMRadioTxt .= "<td>" . boolToYesNo($l['amfm_stereo'])  . "</td>";
	$amFMStereoTapeTxt .= "<td> </td>";
	$cdPlayerTxt .= "<td>" . boolToYesNo($l['compact_disc'])  . "</td>";
	$cdChangerTxt .= "<td> </td>";
	$alloyWheelsTxt .= "<td>" . boolToYesNo($l['alloy_wheels']) . "</td>";
	$powerDoorLocksTxt .= "<td>" . boolToYesNo($l['power_door_locks']) . "</td>";
	$powerMirrorTxt .= "<td>" . boolToYesNo($l['power_mirrors']) . "</td>";
	$sunroofTxt .= "<td>" . boolToYesNo($l['sunroof_moonroof']) . "</td>";
	$gpsTxt .= "<td>" . boolToYesNo($l['navigation']) . "</td>";
	$rearEntTxt .= "<td>" . boolToYesNo($l['rear_entertainment_system']) . "</td>";
	$soldTxt .= "<td> </td>";
	$removeTxt .= "<td><a href='/m/search/compare/?r_vehicle=".$l['vin']."'>Remove</a></td>";
	
	
}



if (!count($listings))
{
	echo 'No vehicles to compare. Please check vehicles on the search pages to view results on this page.';
	
}

?>


	
	


<div class="contnets">
	<div class="inner_wrapper">

<?php
if (count($listings))
{
?>

<table border="0" cellpadding="0" cellspacing="2">

<tr>
	<td> Vehicle </td>
	<?php echo $vehicleTxt; ?>
</tr>

<tr>
	<td> VIN </td>
	<?php echo $vinTxt; ?>
</tr>

<tr>
	<td> Mileage </td>
	<?php echo $mileageTxt; ?>
</tr>

<tr>
	<td> Year </td>
	<?php echo $yearTxt; ?>
</tr>

<tr>
	<td> Make Model </td>
	<?php echo $makeModelTxt; ?>
</tr>

<tr>
	<td> Price </td>
	<?php echo $priceTxt; ?>
</tr>

<tr>
	<td> Car Type </td>
	<?php echo $carTypeTxt; ?>
</tr>

<tr>
	<td> Seller Comments </td>
	<?php echo $sellerCommentsTxt; ?>
</tr>

<tr>
	<td> Body Style </td>
	<?php echo $bodyStyleTxt; ?>
</tr>

<tr>
	<td> Address </td>
	<?php echo $addressTxt; ?>
</tr>

<tr>
	<td> Exterior Color </td>
	<?php echo $extColorTxt; ?>
</tr>

<tr>
	<td> Interior Color </td>
	<?php echo $intColorTxt; ?>
</tr>

<tr>
	<td> Engine </td>
	<?php echo $engineTxt; ?>
</tr>

<tr>
	<td> Transmission </td>
	<?php echo $transTxt; ?>
</tr>

<tr>
	<td> Fuel Type </td>
	<?php echo $fuelTypeTxt; ?>
</tr>

<tr>
	<td> Doors </td>
	<?php echo $doorsTxt; ?>
</tr>

<tr>
	<td> Drive Type </td>
	<?php echo $driveTypeTxt; ?>
</tr>

<tr>
	<td> Driver Air Bag </td>
	<?php echo $driveAirBagTxt; ?>
</tr>


<tr>
	<td> Passenger Air Bag </td>
	<?php echo $passengerAirBagTxt; ?>
</tr>

<tr>
	<td> Side Air Bags </td>
	<?php echo $sideAirBagTxt; ?>
</tr>

<tr>
	<td> Anti-Lock Brakes </td>
	<?php echo $antiLockBrakeTxt; ?>
</tr>

<tr>
	<td> Leather Seats </td>
	<?php echo $leatherSeatTxt; ?>
</tr>

<tr>
	<td> Air Conditioning </td>
	<?php echo $airConditioningTxt; ?>
</tr>


<tr>
	<td> Power Steering </td>
	<?php echo $powerSteeringTxt; ?>
</tr>

<tr>
	<td> Cruise Control </td>
	<?php echo $cruiseControlTxt; ?>
</tr>

<tr>
	<td> Tilt Wheel </td>
	<?php echo $tiltWheelTxt; ?>
</tr>

<tr>
	<td> Power Seats </td>
	<?php echo $powerSeatTxt; ?>
</tr>

<tr>
	<td> Child Seat </td>
	<?php echo $childSeatTxt; ?>
</tr>

<tr>
	<td> Power Windows </td>
	<?php echo $powerWindowTxt; ?>
</tr>

<tr>
	<td> Rear Window Defroster </td>
	<?php echo $rearWindowDefrosterTxt; ?>
</tr>

<tr>
	<td> Tinted Glass </td>
	<?php echo $tintedGlassTxt; ?>
</tr>

<tr>
	<td> AM/FM Radio </td>
	<?php echo $amFMRadioTxt; ?>
</tr>

<tr>
	<td> AM/FM Stereo Tape </td>
	<?php echo $amFMStereoTapeTxt; ?>
</tr>

<tr>
	<td> CD Player </td>
	<?php echo $cdChangerTxt; ?>
</tr>

<tr>
	<td> CD Changer </td>
	<?php echo $cdChangerTxt; ?>
</tr>

<tr>
	<td> Alloy Wheels </td>
	<?php echo $alloyWheelsTxt; ?>
</tr>

<tr>
	<td> Power Door Locks </td>
	<?php echo $powerDoorLocksTxt; ?>
</tr>

<tr>
	<td> Power Mirrors </td>
	<?php echo $powerMirrorTxt; ?>
</tr>

<tr>
	<td> Sunroof/Moonroof </td>
	<?php echo $sunroofTxt; ?>
</tr>

<tr>
	<td> GPS Navigation </td>
	<?php echo $gpsTxt; ?>
</tr>

<tr>
	<td> Rear Entertainment System </td>
	<?php echo $rearEntTxt; ?>
</tr>

<tr>
	<td> Sold </td>
	<?php echo $soldTxt; ?>
</tr>

<tr>
	<td> Remove Vehicle </td>
	<?php echo $removeTxt; ?>
</tr>


</table>

<?php
}
?>

	
	
	</div>
</div>