<?php

class Dealer
{
	public function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	public function getDealers($name, $city, $state)
	{
		if (!strlen($name))
		{
			$name="asresxgdw";
		}
		if (!strlen($city))
		{
			$city="asresxgdw";
		}
		if (!strlen($state))
		{
			$state="asresxgdw";
		}
		
		$sql = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website, u.dealer_logo, u.dealer_image, u.dealer_update, u.profile_right, u.profile_left, a.area_name, a.area_id
	    		FROM users as u
	    		INNER JOIN areas as a on a.user_id = u.user_id
				WHERE u.dealer_name = %s OR u.dealer_city = %s OR u.dealer_state = %s
				ORDER BY u.dealer_name";
		$dealers = $this->db->safeReadQueryAll($sql, $name, $city, $state);
		
		
		return $dealers;
	}
	
	public function getDealerByID($dealerID, $pageStart=1, $pageSize=10)
	{
		$sql = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website, u.dealer_logo, u.dealer_image, u.dealer_update, u.profile_right, u.profile_left,
						a.area_id, a.area_name
	    		FROM areas as a
				INNER JOIN users AS u on u.user_id = a.user_id
	    		WHERE a.area_id = %s 
				";
		$info = $this->db->safeReadQueryFirst($sql, $dealerID);
		
		$dealer['info'] = $info;
		$dealer['listings'] = $this->getDealerListings($info['area_id'], $pageStart, $pageSize);
		
		return $dealer;
			
	}
	
	public function getDealer($dealername, $pageStart=1, $pageSize=10)
	{
		$sql = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website, u.dealer_logo, u.dealer_image, u.dealer_update, u.profile_right, u.profile_left,
						a.area_id, a.area_name
	    		FROM areas as a
				INNER JOIN users AS u on u.user_id = a.user_id
	    		WHERE a.area_name = %s 
				";
		$info = $this->db->safeReadQueryFirst($sql, $dealername);
		
		$dealer['info'] = $info;
		$dealer['listings'] = $this->getDealerListings($info['area_id'], $pageStart, $pageSize);
		
		return $dealer;
			
	}
	
	public function getDealerListings($dealerID, $pageStart=1, $pageSize=10)
	{
		$sql = "
				SELECT SQL_CALC_FOUND_ROWS l.area_id, l.listing_id, l.category, 
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
					LIMIT %d, %d";
		$listings = $this->db->safeReadQueryAll($sql, $dealerID, $pageStart, $pageSize);
		
		$num_total_listings = $this->db->foundRows();
		
		$total_pages = ceil($num_total_listings / $pageSize);
		$listings[0]['total_pages'] = $total_pages;
		$listings[0]['total_listings'] = $num_total_listings;
		
		return $listings;
		
	}
	
	public function getSearchResults($vars)
	{

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
		
		 
		$sqlWhere = "";
		if ($vars['city'])
		{
			$sqlWhere.= " AND l.city = '{$vars['city']}'";
		}
		if ($vars['year'])
		{
			$sqlWhere.= " AND l.year = {$vars['year']}";
		}
		if ($vars['make'])
		{
			$sqlWhere.= " AND l.make = '{$vars['make']}'";
		}
		if ($vars['model'])
		{
			$sqlWhere.= " AND l.model = '{$vars['model']}'";
		}
		if ($vars['car_type'])
		{
			$sqlWhere.= " AND l.car_type = '{$vars['car_type']}'";
		}
		
		if ($vars['price'])
		{
			$priceValues = explode(":", $vars['price']);
			$vars['price_from'] = $priceValues[0];
			$vars['price_to'] = $priceValues[1];
		}
		
		if ($vars['price_from'])
		{
			$sqlWhere.= " AND l.price > {$vars['price_from']}";
		}
		if ($vars['price_to'])
		{
			$sqlWhere.= " AND l.price < {$vars['price_to']}";
		}
		if ($vars['mileage_from'])
		{
			$sqlWhere.= " AND l.mileage > {$vars['mileage_from']}";
		}
		if ($vars['mileage_to'])
		{
			$sqlWhere.= " AND l.mileage < {$vars['mileage_to']}";
		}
		if ($vars['transmission'])
		{
			$sqlWhere.= " AND l.transmission = {$vars['transmission']}";
		}
		if ($vars['engine'])
		{
			$sqlWhere.= " AND l.engine = '{$vars['engine']}'";
		}
		/* Not in DB?
		if ($vars['drive_type'])
		{
			$sqlWhere.= " AND l.drive_type = {$vars['drive_type']}";
		}
		*/
		if ($vars['doors'])
		{
			$sqlWhere.= " AND l.doors = {$vars['doors']}";
		}
		if ($vars['fuel_type'])
		{
			$sqlWhere.= " AND l.fuel_type = '{$vars['fuel_type']}'";
		}
		if ($vars['exterior_color'])
		{
			$sqlWhere.= " AND l.exterior_color = '{$vars['exterior_color']}'";
		}
		if ($vars['interior_color'])
		{
			$sqlWhere.= " AND l.interior_color = '{$vars['interior_color']}'";
		}
		if ($vars['pictures'])
		{
			$sqlWhere.= " AND l.pictures NOT NULL";
		}
		foreach ($featuresArr AS $k => $v)
		{
			if ($vars[$k])
			{
				$sqlWhere .= " AND l.{$k} = 1";	
			}
		}
		
		
		$sql = "
				SELECT SQL_CALC_FOUND_ROWS
									l.area_id, l.listing_id, l.category, 
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
				                	l.is_active, l.create_time, l.expire_time,
						u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website, u.dealer_logo, u.dealer_image, u.dealer_update, u.profile_right, u.profile_left,
						a.area_name						
						FROM listings as l
						INNER JOIN areas as a ON a.area_id = l.area_id
						INNER JOIN users as u ON u.user_id = a.user_id

						WHERE 1 = 1 {$sqlWhere}
				    	ORDER BY l.year, l.make, l.model
						LIMIT {$vars['page_start']}, {$vars['page_size']}";
		$listings = $this->db->safeReadQueryAll($sql, $dealerID);
		
		$num_total_listings = $this->db->foundRows();
		
		$total_pages = ceil($num_total_listings / $vars['page_size']);
		$listings[0]['total_pages'] = $total_pages;
		$listings[0]['total_listings'] = $num_total_listings;
		
		return $listings;
		
	}
	
	public function getDealerListing($vin)
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
						WHERE l.vin = %s";
				    	
		$listing = $this->db->safeReadQueryFirst($sql, $vin);
		
		
		$sql = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.phone, u.email, u.email_visible, u.is_active, u.create_time, u.dealer_name, u.dealer_address, u.dealer_city, u.dealer_state, u.dealer_zip, u.dealer_website, u.dealer_logo, u.dealer_image, u.dealer_update, u.profile_right, u.profile_left,
						a.area_id, a.area_name
	    		FROM areas as a
				INNER JOIN users AS u on u.user_id = a.user_id
	    		WHERE a.area_id = %d 
				";
		$info = $this->db->safeReadQueryFirst($sql, $listing['area_id']);
		
		$dealer['info'] = $info;
		$dealer['listing'] = $listing;
		
		return $dealer;
	}
	
	public function getCompareListings($vehicles)
	{
		$carsList="";
		$xCnt = 0;
		foreach ($vehicles as $v)
		{
			$xCnt++;
			if ($xCnt == count($vehicles))
			{
				$carsList.= "'" . $v . "'";
			}
			else
			{
				$carsList.= "'" . $v . "',";	
			}
		}
		
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
						WHERE l.vin IN ({$carsList})";
				    	
		$listings = $this->db->safeReadQueryAll($sql);
		
		return $listings;
	}
	
	public function getDealerProfileImgs($dealerID, $type="thumb")
	{
		if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/users/'.$dealerID.'/profile/')) 
		{
			$fileList = array();
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != "..") {
		            
		            
		            if ($type == "thumb")
		            {
		            	if (strpos($entry,"thumb"))
			            {
			            	array_push($fileList, $entry);	
			            }
		            }
		            else if ($type !="thumb")
		            {
		            	if (!strpos($entry,"thumb"))
			            {
			            	array_push($fileList, $entry);	
			            }
		            }
		            
		            
		        }
		    }
		    closedir($handle);
		}
		
		return $fileList;
	}
	
	public function getDealerInventoryImgs($dealerID, $type="thumb")
	{
		if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/users/'.$dealerID.'/inventory/')) 
		{
			$fileList = array();
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != "..") {
		            
		            if ($type == "thumb")
		            {
		            	if (strpos($entry,"thumb"))
			            {
			            	array_push($fileList, $entry);	
			            }
		            }
		            else if ($type !="thumb")
		            {
		            	if (!strpos($entry,"thumb"))
			            {
			            	array_push($fileList, $entry);	
			            }
		            }
		            
		        }
		    }
		    closedir($handle);
		}
		
		return $fileList;
	}
	
	public function contactSpamCheck($ipAddr)
	{
		$spamTime = TIME_NOW - 61;
		
		$sql = "SELECT contact_id
				FROM contacts
				WHERE ip_address = %d AND create_time > %d";
		$spam = $this->db->safeReadQueryFirst($sql, $ipAddr, $spamTime);
		
		return $spam;
		
	}
	
	public function contactForm($dealerName)
	{
		$success = false;
		$dealerInfo = $this->getDealer($dealerName);
		
		$spam = $this->contactSpamCheck(ip2Long($_SERVER['REMOTE_ADDR']));
		
		if ($dealerInfo && !$spam)
		{
			$sql = "
			INSERT INTO contacts (area_id, name, email, phone, comment, create_time, ip_address)
			VALUES (%d, %s, %s, %s, %s, %d, %d)
            ";
		
			$fullName = $_POST['first_name'] . ' ' . $_POST['last_name'];
			
			$this->db->safeQuery($sql,
							$dealerInfo['info']['area_id'],
	        				$fullName,
							$_POST['email'],
							$_POST['phone'],
							$_POST['msg'],
							TIME_NOW,
							ip2Long($_SERVER['REMOTE_ADDR'])
							);
			$success = 1;
		}
		
		return $success;
		
	}
	
	public function getMakes()
	{
		$sql = "SELECT m.make 
				FROM makesmodels as m
				INNER JOIN listings as l on l.make=m.make
				GROUP BY m.make";
				
		$makes = $this->db->safeReadQueryAll($sql);
		
		$makeArr = array();
		foreach ($makes as $m)
		{
			
			$sql = "SELECT count(l.listing_id) as cnt
					FROM listings as l
					WHERE l.make = %s AND l.is_active = 1";
			$listings = $this->db->safeReadQueryFirst($sql, $m['make']);
			
			$makeArr[$m['make']]['make'] = $m['make'];
			$makeArr[$m['make']]['count'] = $listings['cnt'];
			
		}
		
		return $makeArr;
		
	}
	
	public function getAccountBasic($userID)
	{
		$sql = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.email
				FROM users as u
				WHERE u.user_id = %d";
			
		$user = $this->db->safeReadQueryFirst($sql, $userID);
		
		return $user;
	}
	
	public function updateAccountBasic($vars, $userID)
	{
		
		$sql = "UPDATE users
				SET first_name = %s,
					last_name = %s,
					email = %s
				WHERE user_id = %d";
			
		$this->db->safeQuery($sql, $vars['first_name'], $vars['last_name'], $vars['email'], $userID);
		
		
		if (strlen($vars['passwd']))
		{
			if ($vars['passwd'] == $vars['passwd_confirm'])
			{
				$sql = "UPDATE users
				SET password = %s
				WHERE user_id = %d";
			
				$this->db->safeQuery($sql, md5($vars['passwd']), $userID);
			}
			
		}
		
		return true;
	}
	
	public function saveSearch($vars, $userID)
	{
		$randNum = rand(1, 999);
		$searchName = "search - " . $randNum;
		$searchType = ($vars['dealer_id']) ? 'dealer' : 'search';
		$searchType = ($vars['listing_id']) ? 'listing' : $searchType;
		
		$areaID = ($vars['dealer_id']) ? $vars['dealer_id'] : 0;
		$listingID = ($vars['listing_id']) ? $vars['listing_id'] : 0;
		
		$sql = "INSERT INTO user_searches
				(user_id, area_id, listing_id, type, name, details, create_time)
				VALUES (%d, %d, %s, %s, %s, %s, %d)";
		$this->db->safeQuery($sql, $userID, $areaID, $listingID, $searchType, $searchName, serialize($vars), TIME_NOW);
		
		return true;		
	}
	
	public function updateSearch($vars, $userID)
	{
		foreach ($vars['name'] as $k => $v)
		{
			$sql = "UPDATE user_searches
					SET name = %s
					WHERE user_id = %d AND search_id = %d";
			$this->db->safeQuery($sql, $v, $userID, $k);
		}
		
		return true;		
	}
	
	public function getUserSearches($userID)
	{
		$sql = "SELECT search_id, name, area_id, type, listing_id
				FROM user_searches
				WHERE user_id = %d";
		$searches = $this->db->safeReadQueryAll($sql, $userID);
		
		return $searches;
	}
	
	public function getSearch($searchID, $userID)
	{
		
		$sql = "SELECT search_id, name, details
				FROM user_searches
				WHERE user_id = %d AND search_id = %d";
		$search = $this->db->safeReadQueryFirst($sql, $userID, $searchID);
		
		return $search;
	}
	
	public function removeSearch($searchID, $userID)
	{
		$sql = "DELETE FROM user_searches
				WHERE user_id = %d AND search_id = %d";
		$this->db->safeQuery($sql, $userID, $searchID);
		
		return true;
	}
	
	public function createAccount($vars)
	{
		
		$error = "";
		$sql = "SELECT user_id
				FROM users
				WHERE username = %s";
		
		$userInfo = $this->db->safeReadQueryFirst($sql, $vars['username']);
		
		if ($userInfo)
		{
			$error = "The username requested is taken. Please choose another.";
			return $error;
		}
		
		if (!strlen($vars['first_name']) || 
			!strlen($vars['last_name']) ||
			!strlen($vars['email']) ||
			!strlen($vars['username']) ||
			!strlen($vars['passwd']))
			{
				
				$error = "All fields are required to create an account.";
				return $error;
			}
		
		$sql = "INSERT INTO users
				(first_name, last_name, email, username, password)
				VALUES (%s, %s, %s, %s, %s)";
		$this->db->safeQuery($sql, $vars['first_name'], $vars['last_name'], $vars['email'], $vars['username'], md5($vars['passwd']));
		
		return 1;
		
	}
	
	public function createDealerAccount()
	{
		$sql = "SELECT admin_id from admin
				WHERE user_id = %d";
		$admin = $this->db->safeReadQueryFirst($sql, $_SESSION['user_id']);
		if ($admin || !$_SESSION['user_id'])
		{
			return false;
		}
		
		$userInfo = $this->getUserInfo($_SESSION['area_id']);
		$areaName = $userInfo['username'] . ' @'.$userInfo['user_id'];
		$sql = "INSERT INTO areas (user_id, area_name, is_active)
				VALUES (%d, %s, %d)
				";
		$this->db->safeQuery($sql, $_SESSION['user_id'], $areaName);
		
		$sql = "INSERT INTO admin (user_id, create_time, modify_time, is_public, is_active, is_global_admin)
				VALUES (%d, %d, %d, %d, %d, %d)";
				
		$this->db->safeQuery($_SESSION['user_id'], TIME_NOW, TIME_NOW, 1, 1, 0);
		$adminID = $this->db->insertID();
		
		$dealerRole = 2;
		$sellerRole = 3;
		
		$sql = "INSERT INTO area_admin (admin_id, region_id, admin_role_id, admin_group_id, position)
				VALUES (%d, %d, %d, %d, %s)";
		$this->db->safeQuery($sql, $adminID, 1, $dealerRole, 1, 'Admin');
		
		return true;
		
		
	}
	
	public function getPackages()
	{
		$sql = "SELECT package_id, category, name, description, price, listing_qty_limit as qty, listing_time_limit as days, is_active
				FROM packages
				WHERE is_active = 1";
		$packages = $this->db->safeReadQueryAll($sql);
		
		return $packages;
	}
	
	public function getPackage($packageID)
	{
		$sql = "SELECT package_id, category, name, description, price, listing_qty_limit as qty, listing_time_limit as days, is_active
				FROM packages
				WHERE is_active = 1 AND package_id = %d";
		$package = $this->db->safeReadQueryFirst($sql, $packageID);
		
		return $package;
	}
	
	public function getUserInfo($userID)
	{
		$sql = "SELECT u.user_id, u.username, u.email, u.is_active
				FROM users as u
				WHERE u.user_id = %d";
		
		$userInfo = $this->db->safeReadQueryFirst($sql, $userID);
		
		return $userInfo;
	}
	
	public function hasAdminAccount($userID)
	{
		$sql = "SELECT admin_id
				from admin
				WHERE user_id = %d AND is_active = 1";
		$result = $this->db->safeReadQueryFirst($sql, $userID);
		
		return $result;
	}
	
	
}

?>	