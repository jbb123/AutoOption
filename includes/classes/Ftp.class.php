<?php

class FTP
{
	public function __construct()
	{
		global $db;
		$this->db = $db;
		$this->ftpUser = "auto";
		$this->ftpPass = "auto";
		$this->ftpHost = "auto.bit30.com"; 
		
	}
	
    public function getFileList($path = ".", $fileType = ".csv")
    {
    	$conn = ftp_connect($this->ftpHost);
		$login_result = ftp_login($conn, $this->ftpUser, $this->ftpPass);
		
		$fileList = ftp_nlist($conn, $path);
		
		$allFiles = array();
		//Only .csv files
		foreach ($fileList as $f)
		{
			if (strpos($f, $fileType))
			{
				$cleanFileName = str_replace("./", "", $f);
				$cleanFileName = str_replace("/users/profile/", "", $f);
				$cleanFileName = str_replace("/users/inventory/", "", $f);
				array_push($allFiles, $cleanFileName);
			}
		}
		
		return $allFiles;
		
    }
    
    public function getFileContent($filename)
    {
    	$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, "ftp://{$this->ftpUser}:{$this->ftpPass}@{$this->ftpHost}/{$filename}");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		curl_close($curl);
		
		$result = explode("\n", $result);
		
		$inventory = array();
		array_shift($result);
		
		foreach ($result as $r)
		{
			$row = str_getcsv($r, ',', '"');
			array_push($inventory, $row);
		}
		
		return $inventory;
    }
    
    public function deleteFile($filename)
    {
    	$conn = ftp_connect($this->ftpHost);
		$login_result = ftp_login($conn, $this->ftpUser, $this->ftpPass);
    	
    	ftp_delete($conn, $filename);
    	
    	return true;
    }
    
    public function import($data)
    {
    
    $username = $data[0][2];
    
    $sql = "SELECT u.user_id, u.username, a.area_id
			FROM users as u
			INNER JOIN areas as a ON a.user_id = u.user_id
			where username = %s";
    $userInfo = $this->db->safeReadQueryFirst($sql, $username);
    
    if (!userInfo)
    {
    	return false;
    }
    
    $areaID = $userInfo['area_id'];
    
    	foreach ($data as $d)
    	{
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
            $this->db->safeQuery($sql,
            				$areaID, $d[1],
            				$d[4], $d[6],
            				$d[5], $d[16],
            				$d[17], $d[18],
            				$d[20], $d[22],
            				$d[23], $d[19],
            				$d[21], $d[24],
            				$d[40], $d[26],
            				$d[29], $d[35],
            				$d[11], $d[7],
            				$d[10], $d[28],
            				$d[13], $d[14],
            				$d[15], $d[27],
            				$d[42], $d[12],
            				$d[30], $d[33],
            				$d[34], $d[36],
            				$d[37], $d[38],
            				$d[39], $d[41],
            				$d[43], $d[44],
            				$d[45], $d[46],
            				$d[47], $d[48],
            				$d[49], $d[50],
            				$d[52], $d[53],
            				$d[54], $d[55],
            				$d[56], $d[57],
            				1, TIME_NOW, 0);
          			    
    	}
    	return true;
    }
}
?>
