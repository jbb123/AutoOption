<?php

include_once('includes/init.inc.php');
AdminFunction::requireAccess('LISTING_LIST');

$errors = array();

function save()
{
	global $db, $errors;

    $success = false;

    if ($_POST['grant'] != 'w')
    {
        return false;
    }
    
    if (in_array($_REQUEST['action'], array('create', 'edit')))
    {
    	if (empty($_POST['category'])) Error::raise("You must provide a listing name.");
    }
    
    if (Error::hasErrors())
    {
        return false;
    }
 	
    
    switch ($_REQUEST['action'])
    {
        case 'create':
            
            $sql = "
                INSERT INTO listings (area_id, category, 
                					keywords, views, 
                					featured, price, 
                					seller_comments, body_style, 
                					exterior_color, interior_color, 
                					doors, address, 
                					city, state, 
                					zipcode, listing_rating, 
                					engine, fuel_type, 
                					vin, pictures, 
                					video, youtube_video_id, 
                					car_type, make, 
                					model, driver_air_bag, 
                					year, mileage, 
                					passenger_air_bag, transmission, 
                					anti_lock_brakes, leather_seats, 
                					air_conditioning, sold, 
                					power_steering, cruise_control, 
                					tilt_wheel, power_seats, 
                					child_seat, power_window, 
                					rear_window, tinted_glass, 
                					amfm_stereo, compact_disc, 
                					alloy_wheels, power_door_locks, 
                					power_mirrors, sunroof_moonroof, 
                					navigation, rear_entertainment_system, 
                					is_active, create_time, expire_time)
                VALUES (%d, %s, 
                		%s, %d,
                		%d, %f,
                		%s, %s,
                		%s, %s,
                		%d, %s,
                		%s, %s,
                		%s, %s,
                		%s, %s,
                		%s, %s,
                		%s, %s,
                		%s, %s,
                		%s, %d,
                		%d, %d, 
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d,
                		%d, %d, %d
                		) 
            ";
            $db->safeQuery($sql,
            				$_SESSION['area_id'], $_POST['category'],
            				$_POST['keywords'], $_POST['views'],
            				$_POST['featured'], $_POST['price'],
            				$_POST['seller_comments'], $_POST['body_style'],
            				$_POST['exterior_color'], $_POST['interior_color'],
            				$_POST['doors'], $_POST['address'],
            				$_POST['city'], $_POST['state'],
            				$_POST['zipcode'], $_POST['listing_rating'],
            				$_POST['engine'], $_POST['fuel_type'],
            				$_POST['vin'], $_POST['pictures'],
            				$_POST['video'], $_POST['youtube_video_id'],
            				$_POST['car_type'], $_POST['make'],
            				$_POST['model'], $_POST['driver_air_bag'],
            				$_POST['year'], $_POST['mileage'],
            				$_POST['passenger_air_bag'], $_POST['transmission'],
            				$_POST['anti_lock_brakes'], $_POST['leather_seats'],
            				$_POST['air_conditioning'], $_POST['sold'],
            				$_POST['power_steering'], $_POST['cruise_control'],
            				$_POST['tilt_wheel'], $_POST['power_seats'],
            				$_POST['child_seat'], $_POST['power_window'],
            				$_POST['rear_window'], $_POST['tinted_glass'],
            				$_POST['amfm_stereo'], $_POST['compact_disc'],
            				$_POST['alloy_wheels'], $_POST['power_door_locks'],
            				$_POST['power_mirrors'], $_POST['sunroof_moonroof'],
            				$_POST['navigation'], $_POST['rear_entertainment_system'],
            				$_POST['is_active'], TIME_NOW, 0
						    );
            $listing_id = (int)$db->insertId();
			
			AdminAudit::log('LISTING_ADD',$listing_id);
		break;
		
        case 'edit':
        	$sql = "
                UPDATE listings SET
                    category = %s, 
                    keywords = %s, 
   					views = %d, 
					featured = %d, 
					price = %f, 
					seller_comments = %s, 
					body_style = %s, 
      				exterior_color = %s, 
      				interior_color = %s, 
      				doors = %d, 
      				address = %s, 
  					city = %s, 
  					state = %s, 
  					zipcode = %s, 
  					listing_rating = %s, 
   					engine = %s, 
   					fuel_type = %s, 
   					vin = %s, 
   					pictures = %s, 
   					video = %s, 
   					youtube_video_id = %s, 
   					car_type = %s, 
   					make = %s, 
   					model = %s, 
   					driver_air_bag = %d, 
   					year = %d, 
   					mileage = %d, 
   					passenger_air_bag = %d, 
   					transmission = %d, 
   					anti_lock_brakes = %d, 
   					leather_seats = %d, 
   					air_conditioning = %d, 
   					sold = %d, 
   					power_steering = %d, 
   					cruise_control = %d, 
   					tilt_wheel = %d, 
   					power_seats = %d, 
   					child_seat = %d, 
   					power_window = %d, 
   					rear_window = %d, 
   					tinted_glass = %d, 
   					amfm_stereo = %d, 
   					compact_disc = %d, 
   					alloy_wheels = %d, 
   					power_door_locks = %d, 
   					power_mirrors = %d, 
   					sunroof_moonroof = %d, 
   					navigation = %d, 
   					rear_entertainment_system = %d, 
   					is_active = %d, 
   					create_time = %d, 
   					expire_time = %d
                WHERE listing_id = %d AND area_id = %d
            ";
            
            $db->safeQuery($sql,
               				$_POST['category'],
               				$_POST['keywords'], 
               				$_POST['views'],
            				$_POST['featured'], 
            				$_POST['price'],
            				$_POST['seller_comments'], 
            				$_POST['body_style'],
            				$_POST['exterior_color'], 
            				$_POST['interior_color'],
            				$_POST['doors'], 
            				$_POST['address'],
            				$_POST['city'], 
            				$_POST['state'],
            				$_POST['zipcode'], 
            				$_POST['listing_rating'],
            				$_POST['engine'], 
            				$_POST['fuel_type'],
            				$_POST['vin'], 
            				$_POST['pictures'],
            				$_POST['video'], 
            				$_POST['youtube_video_id'],
            				$_POST['car_type'], 
            				$_POST['make'],
            				$_POST['model'], 
            				$_POST['driver_air_bag'],
            				$_POST['year'], 
            				$_POST['mileage'],
            				$_POST['passenger_air_bag'], 
            				$_POST['transmission'],
            				$_POST['anti_lock_brakes'], 
            				$_POST['leather_seats'],
            				$_POST['air_conditioning'], 
            				$_POST['sold'],
            				$_POST['power_steering'], 
            				$_POST['cruise_control'],
            				$_POST['tilt_wheel'], 
            				$_POST['power_seats'],
            				$_POST['child_seat'], 
            				$_POST['power_window'],
            				$_POST['rear_window'], 
            				$_POST['tinted_glass'],
            				$_POST['amfm_stereo'], 
            				$_POST['compact_disc'],
            				$_POST['alloy_wheels'], 
            				$_POST['power_door_locks'],
            				$_POST['power_mirrors'], 
            				$_POST['sunroof_moonroof'],
            				$_POST['navigation'], 
            				$_POST['rear_entertainment_system'],
            				$_POST['is_active'], 
            				TIME_NOW, 
            				0,
            				$_POST['listing_id'],
            				$_SESSION['area_id']);
				
            $success = (bool)$db->affectedRows();
			
			AdminAudit::log('LISTING_UPDATE', $_POST['listing_id']);
            break;
		
		case 'remove':
			$sql = "DELETE FROM listings
					WHERE listing_id = %d AND area_id = %d";
			$db->safeQuery($sql, $_REQUEST['listing_id'], $_SESSION['area_id']);
			AdminAudit::log('LISTING_DELETE', $_POST['listing_id']);
			break;
       
    }
    return true;
}

$form_query = false;


switch ($_REQUEST['action'])
{
    case 'create':
      	$display = 'form';
      	$action_title = 'Create New Listing';
      	if (save())
      	{
      		showManageRedirect('Listing was successfully created.  Redirecting to Listings...', THIS_SCRIPT);
      	}
      break;

    case 'edit':
      	$display = 'form';
      	$action_title = 'Update Listing';
      	if (save())
      	{
       		showManageRedirect('Listing was successfully updated.  Redirecting to Listings...', THIS_SCRIPT);
      	}
      break;
      
	case 'remove':
      	if (save())
      	{
       		showManageRedirect('Listing was successfully removed.  Redirecting to Listings...', THIS_SCRIPT);
      	}
      break;
     
    default:
    	$display = 'list';
    break;
}

if ($display == 'form')
{
	
    if (isset($_REQUEST['listing_id']))
    {
    	$sql = "
    		SELECT l.area_id, l.listing_id, l.category, 
					l.keywords, l.views, 
                	l.featured, l.price, 
                	l.seller_comments, l.body_style, 
                	l.exterior_color, l.interior_color, 
                	l.doors, l.address, 
                	l.city, l.state, 
                	l.zipcode, l.listing_rating, 
                	l.engine, l.fuel_type, 
                	l.vin, l.pictures, 
                	l.video, l.youtube_video_id, 
                	l.car_type, l.make, 
                	l.model, l.driver_air_bag, 
                	l.year, l.mileage, 
                	l.passenger_air_bag, l.transmission, 
                	l.anti_lock_brakes, l.leather_seats, 
                	l.air_conditioning, l.sold, 
                	l.power_steering, l.cruise_control, 
                	l.tilt_wheel, l.power_seats, 
                	l.child_seat, l.power_window, 
                	l.rear_window, l.tinted_glass, 
                	l.amfm_stereo, l.compact_disc, 
                	l.alloy_wheels, l.power_door_locks, 
                	l.power_mirrors, l.sunroof_moonroof, 
                	l.navigation, l.rear_entertainment_system, 
                	l.is_active, l.create_time, l.expire_time
			FROM listings as l
    		WHERE l.listing_id = %d
		";
        $fields = $db->safeReadQueryFirst($sql, $_REQUEST['listing_id']);
    }
    
    if (isset($_POST['action']))
    {
        $fields = array_merge((array)$fields, $_POST);
    }

    $fields = htmlEncode($fields);
}
else
{
	$sql = "
    	SELECT l.area_id, l.listing_id, l.category, 
					l.keywords, l.views, 
                	l.featured, l.price, 
                	l.seller_comments, l.body_style, 
                	l.exterior_color, l.interior_color, 
                	l.doors, l.address, 
                	l.city, l.state, 
                	l.zipcode, l.listing_rating, 
                	l.engine, l.fuel_type, 
                	l.vin, l.pictures, 
                	l.video, l.youtube_video_id, 
                	l.car_type, l.make, 
                	l.model, l.driver_air_bag, 
                	l.year, l.mileage, 
                	l.passenger_air_bag, l.transmission, 
                	l.anti_lock_brakes, l.leather_seats, 
                	l.air_conditioning, l.sold, 
                	l.power_steering, l.cruise_control, 
                	l.tilt_wheel, l.power_seats, 
                	l.child_seat, l.power_window, 
                	l.rear_window, l.tinted_glass, 
                	l.amfm_stereo, l.compact_disc, 
                	l.alloy_wheels, l.power_door_locks, 
                	l.power_mirrors, l.sunroof_moonroof, 
                	l.navigation, l.rear_entertainment_system, 
                	l.is_active, l.create_time, l.expire_time
		FROM listings as l
		WHERE l.area_id = %d
    	ORDER BY l.year, l.make, l.model
    ";
    
    $listings = $db->safeReadQueryAll($sql, $_SESSION['area_id']);
    
}

include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');

if ($display == 'form')
{
	
	echo '<h1>'.$action_title .'</h1>';
	echo Error::getErrorList();
	
	echo '<table border=0 width="100%"><tr><td valign="top">';
	
	$featuresArr = array();
	$featuresArr['passenger_air_bag'] = "Passenger Air Bag";
	$featuresArr['transmission'] = "Transmission";
	$featuresArr['anti_lock_brakes'] = "Anti Lock Brakes";
	$featuresArr['leather_seats'] = "Leather Seats";
	$featuresArr['air_conditioning'] = "Air Conditioning";
	$featuresArr['power_steering'] = "Power Steering";
	$featuresArr['cruise_control'] = "Cruise Control";
	$featuresArr['tilt_wheel'] = "Tilt Wheel";
	$featuresArr['power_seats'] = "Power Seats";
	$featuresArr['rear_window'] = "Rear Window";
	$featuresArr['tinted_glass'] = "Tinted Glass";
	$featuresArr['amfm_stereo'] = "AM/FM Stereo";
	$featuresArr['compact_disc'] = "Compact Disc";
	$featuresArr['alloy_wheels'] = "Alloy Wheels";
	$featuresArr['power_door_locks'] = "Power Door Locks";
	$featuresArr['sunroof_moonroof'] = "Sun Roof/Moon Roof";
	$featuresArr['navigation'] = "Navigation";
	$featuresArr['rear_entertainment_system'] = "Rear Entertainment System"; 
	
	echo '<form action="'. THIS_SCRIPT.'" method="post">
	    <input type="hidden" name="action" value="'.$_REQUEST['action'].'" />
	    <input type="hidden" name="grant" value="w" />
		<input type="hidden" name="listing_id" value="'.$fields['listing_id'].'" />
	
	    <div class="req">Required Field</div><br />
	
	    <fieldset>
	        <legend>Listing Information</legend>
			<div class="lbl req">Listing ID</div><div class="data">' . $fields['listing_id'] . '<br><br></div>
			<div class="lbl req">Category</div><div class="data"><input type="text" name="category" value="'.$fields['category'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Keywords</div><div class="data"><input type="text" name="keywords" value="'.$fields['keywords'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Featured</div><div class="data"><select name="featured">'.getFormOptions($yesNoDrop, $fields['featured']).'</select><br><br></div>
			<div class="lbl req">Price</div><div class="data"><input type="text" name="price" value="'.$fields['price'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Body Style</div><div class="data"><input type="text" name="body_style" value="'.$fields['body_style'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Exterior Color</div><div class="data"><input type="text" name="exterior_color" value="'.$fields['exterior_color'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Interior Color</div><div class="data"><input type="text" name="interior_color" value="'.$fields['interior_color'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Doors</div><div class="data"><input type="text" name="doors" value="'.$fields['doors'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Address</div><div class="data"><input type="text" name="address" value="'.$fields['address'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">City</div><div class="data"><input type="text" name="city" value="'.$fields['city'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">State</div><div class="data"><input type="text" name="state" value="'.$fields['state'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Zip</div><div class="data"><input type="text" name="zipcode" value="'.$fields['zipcode'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Listing Rating</div><div class="data"><input type="text" name="listing_rating" value="'.$fields['listing_rating'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Engine</div><div class="data"><input type="text" name="engine" value="'.$fields['engine'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Fuel Type</div><div class="data"><input type="text" name="fuel_type" value="'.$fields['fuel_type'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Vin</div><div class="data"><input type="text" name="vin" value="'.$fields['vin'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Pictures</div><div class="data"><textarea name="seller_comments" rows="6" cols="75">'.$fields['pictures'].'</textarea><br><br></div>
			<div class="lbl req">Video</div><div class="data"><textarea name="video" rows="6" cols="75">'.$fields['video'].'</textarea><br><br></div>
			<div class="lbl req">Youtube Video ID</div><div class="data"><input type="text" name="youtube_video_id" value="'.$fields['youtube_video_id'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Car Type</div><div class="data"><input type="text" name="car_type" value="'.$fields['car_type'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Make</div><div class="data"><input type="text" name="make" value="'.$fields['make'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Model</div><div class="data"><input type="text" name="model" value="'.$fields['model'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Year</div><div class="data"><input type="text" name="year" value="'.$fields['year'].'" maxlength="50" class="lg"><br><br></div>
			<div class="lbl req">Mileage</div><div class="data"><input type="text" name="mileage" value="'.$fields['mileage'].'" maxlength="50" class="lg"><br><br></div>
			
			<div class="lbl req">Features</div><div class="data">';
				
				$x=0;
				foreach ($featuresArr AS $k => $v)
				{
					$x++;
					echo '<input type="checkbox" name="'.$k.'" value="1"> '. $v . '&nbsp;&nbsp;&nbsp;';
				}
	
			
			
			echo '<br><br></div>
			
			<div class="lbl req">Sold</div><div class="data"><select name="sold">'.getFormOptions($yesNoDrop, $fields['sold']).'</select><br><br></div>
			
			<div class="lbl req">Seller Comments</div><div class="data"><textarea name="seller_comments" rows="6" cols="40">'.$fields['seller_comments'].'</textarea><br><br></div>
			
			<div class="lbl req">Active</div><div class="data"><select name="is_active">'.getFormOptions($activeDrop, $fields['is_active']).'</select><br><br></div>
		</fieldset>
	
	    <a href="'.THIS_SCRIPT.'" style="float: left;"> Cancel </a>
	    <input type="submit" value="'.$action_title.'" style="float:right;"/>
	</form>';
	
	echo '</td></tr></table>';
	
}

else
{
	echo '<h1>List Inventory</h1>';
    echo '<a href="' . THIS_SCRIPT . '?action=create">Add Listing</a>  <br /><br />';
    echo '<table class="list">';
    echo '<tr>
    	<th>Listing ID</th>
    	<th>Year/Make/Model/</th>
    	<th>Price</th>
    	<th>Active</th>
    	<th>Actions</th>
    	</tr>';

    if ($listings)
	{
		foreach ($listings as $k => $l)
		{
			$rowClass = $k % 2 ? 'row0' : 'row1';
			
			echo '<tr class="' . $rowClass . '">';
			echo '<td class="alt1">' . $l['listing_id'] . '</td>';
			echo '<td class="alt2">' . $l['year'] . ' ' . $l['make'] . ' ' . $l['model'] . '</td>';
			echo '<td class="alt1">' . $l['price'] . '</td>';
			echo '<td class="alt2">' . boolToYesNo($l['is_active']) . '</td>';
			echo '<td class="alt1"><a href="' . THIS_SCRIPT . '?action=edit&listing_id=' . $l['listing_id'] . '">Edit Listing</a> | <a href="' . THIS_SCRIPT . '?action=remove&listing_id=' . $l['listing_id'] . '">Remove Listing</a> </td>';
			echo '</tr>';
		}
	}
	else
	{
	echo '<tr><td colspan="8">No listings found.</td></tr>';
	}

    echo '</table>';
}


include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
?>