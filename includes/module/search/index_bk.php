<?php

$subPage = $path_split[4];

if ($subPage=="results")
{
		require_once('results.php');
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
	
	<div class="formcontainer">
            <div id="titletext">Search</div>
            <div class="formbox">
            <form action="/m/search/results" id="cardbform" method="post" accept-charset="utf-8">
                <div class="options">            
                    <div class="normtext">Year</div> 
                    <div class="selectbox">
                    <select name="year" id="year" size="1">
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
                </div>
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Make</div> 
                    <div class="selectbox">
                    <select name="make" id="make" size="1">
                        <option value="">-- All Makes --</option> 
                    </select>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Model</div> 
                    <div class="selectbox">
                    <select name="model" id="model" size="1">
                        <option value="">-- All Models --</option> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Car Type</div> 
                    <div class="selectbox">
                    <select name="car_type" id="car_type" size="1">
                        <?php echo getFormOptions($carTypes, $fields['car_type']); ?> 
                    </select>
                    </div>
                </div>
                
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Price</div> 
                    <div class="normtext">
                    <input type="text name="price_from"> To <input type="text name="price_to">
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Mileage</div> 
                    <div class="normtext">
                    <input type="text name="mileage_from"> To <input type="text name="mileage_to">
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Transmission</div> 
                    <div class="selectbox">
                    <select name="transmission" id="transmission" size="1">
                        <?php echo getFormOptions($carTransmission, $fields['transmission']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Engine</div> 
                    <div class="selectbox">
                    <select name="engine" id="engine" size="1">
                        <?php echo getFormOptions($carEngine, $fields['engine']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Drive Type</div> 
                    <div class="selectbox">
                    <select name="drive_type" id="drive_type" size="1">
                        <?php echo getFormOptions($carDriveType, $fields['drive_type']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Doors</div> 
                    <div class="selectbox">
                    <select name="doors" id="doors" size="1">
                        <?php echo getFormOptions($carDoors, $fields['doors']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Fuel Type</div> 
                    <div class="selectbox">
                    <select name="fuel_type" id="fuel_type" size="1">
                        <?php echo getFormOptions($carFuelType, $fields['fuel_type']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Exterior Color</div> 
                    <div class="selectbox">
                    <select name="exterior_color" id="exterior_color" size="1">
                        <?php echo getFormOptions($carExtColor, $fields['exterior_color']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Interior Color</div> 
                    <div class="selectbox">
                    <select name="interior_color" id="interior_color" size="1">
                        <?php echo getFormOptions($carIntColor, $fields['interior_color']); ?> 
                    </select>
                    </div>
                </div>
                
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">With pictures</div> 
                    <div class="normtext">
                    <input type="checkbox" name="pictures" value="1">
                    </div>
                </div>
             
                <div class="clear"></div>
                <div class="options">            
                    <div class="normtext">Features</div> 
                    <div class="normtext">
                    <?php
                    foreach ($featuresArr AS $k => $v)
					{
						$x++;
						echo '<input type="checkbox" name="'.$k.'" value="1"> '. $v . '<br>';
					}
                    ?>
                    </div>
                </div>
                
                
                <div class="clear"></div>
                <div class="options">            
                    <input type="submit" name="action" value=" Search ">    
                </div>
                </div>
                
                </form>
            </div>
            <div class="clear"></div>
        </div>
        
	<br><br><br>
	
	</div>			
	
</div>






