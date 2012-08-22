<?php

$subPage = $path_split[3];

if ($subPage=="results")
{
		require_once('results.php');
		include_once('templates/footer.inc.php');
		die;
}

if ($subPage=="compare")
{
		require_once('compare.php');
		include_once('templates/footer.inc.php');
		die;
}

include_once ('ajax/config.inc.php');

include_once (PATH_CLASS.'Dealer.class.php');

include_once ('vars.inc.php');

?>

<div class="contents">
	<div class="inner_wrapper">
	
	<?php
	if ($success)
	{
		echo 'Thank you for contacting this dealer.<br><br>';
	}
	?>
	
	
		<div class="left_column">
			<div class="widgets">
				<img alt="cauley" src="/images/cauley.jpg">
			</div>
			<div class="widgets">
				<img alt="star" src="/images/star.jpg">
			</div>
		</div>	
	
	
	<div class="middle_column" style="padding: 20px 0 0 20px;">
	<div class="formcontainer">
	
	
		<div class="box">
			<div class="boxArea">
				<div class="boxContent">


           <div class="formbox">
            <form action="/m/search/results/" id="cardbform" method="post" accept-charset="utf-8">
            			<dl>
			<dd>
            <fieldset>
            	<legend>Search Inventory</legend>
                <div class="optional">            
                    <label>Year</label> 
                    <select name="year" id="year" size="1" class="selectOne">
                        <option value="">-- All Years --</option> 
                        <?php
                             // Lets get the years
                            $vehyears = $cl->getyears();
                            while($row = mysql_fetch_row($vehyears)){
                                echo '<option value="'.$row[0].'">'.$row[0].'</option>/n';
                            };
                        ?>
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Make</label> 
                    <select name="make" id="make" size="1" class="selectOne">
                        <option value="">-- All Makes --</option> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Model</label> 
                    <select name="model" id="model" size="1" class="selectOne">
                        <option value="">-- All Models --</option> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Car Type</label> 
                    <select name="car_type" id="car_type" size="1" class="selectOne">
                        <?php echo getFormOptions($carTypes, $fields['car_type']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Price</label> 
                    <input type="text" name="price_from" class="inputText" size="10" maxlength="100" style="width: 75px"> To <input type="text" name="price_to" style="width: 75px">
                </div>
                
                <div class="optional">            
                    <label>Mileage</label> 
                    <input type="text" name="mileage_from" class="inputText" size="10" maxlength="100" style="width: 75px"> To <input type="text" name="mileage_to" style="width: 75px">
                </div>
                
                <div class="optional">            
                    <label>Transmission</label> 
                    <select name="transmission" id="transmission" size="1" class="selectOne">
                        <?php echo getFormOptions($carTransmission, $fields['transmission']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Engine</label> 
                    <select name="engine" id="engine" size="1" class="selectOne">
                        <?php echo getFormOptions($carEngine, $fields['engine']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Drive Type</label> 
                    <select name="drive_type" id="drive_type" size="1" class="selectOne">
                        <?php echo getFormOptions($carDriveType, $fields['drive_type']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Doors</label> 
                    <select name="doors" id="doors" size="1" class="selectOne">
                        <?php echo getFormOptions($carDoors, $fields['doors']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Fuel Type</label> 
                    <select name="fuel_type" id="fuel_type" size="1" class="selectOne">
                        <?php echo getFormOptions($carFuelType, $fields['fuel_type']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Exterior Color</label> 
                    <select name="exterior_color" id="exterior_color" size="1" class="selectOne">
                        <?php echo getFormOptions($carExtColor, $fields['exterior_color']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>Interior Color</label> 
                    <select name="interior_color" id="interior_color" size="1" class="selectOne">
                        <?php echo getFormOptions($carIntColor, $fields['interior_color']); ?> 
                    </select>
                </div>
                
                <div class="optional">            
                    <label>With pictures</label> 
                    <input type="checkbox" name="pictures" value="1" class="inputText" size="10" maxlength="100" style="width: 20px">
                </div>
             	</fieldset>
             	<fieldset>
             		<legend>Features</legend>
                <div class="optional">            
                    
                    <?php
                    foreach ($featuresArr AS $k => $v)
					{
						$x++;
						echo '<label class="labelCheckbox"><input type="checkbox" name="'.$k.'" class="inputCheckbox" value="1" /> '. $v .'</label>';
					}
                    ?>
                </div>
                
				</fieldset>

                <div class="optional">            
                    <input type="submit" name="action" value=" Search ">    
                </div>
                </div>
 					
					</dd>
					</dl>               
                </form>
            </div>
</div>
					
				</div>
			</div>
		</div>		
	
 
        </div>
        
	<br><br><br>
	
	</div>			
	
</div>






