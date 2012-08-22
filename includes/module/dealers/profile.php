<?php

include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();


$dealerName = $path_split[3];

$profile = $dealer->getDealer($dealerName);

?>


<div class="contents">
	<div class="inner_wrapper">
<?php
include_once('header.php');
?>

	<div class="headingArea">
		
		<div class="links">
			<label><img src="/images/print.gif" alt="Print" /><a href="javascript:window.print()">Print This Ad</a></label><label><img src="/images/cal.gif" alt="Cal" /><a href="#">Loan Calculator</a></label>
			<span class='st_facebook_large' displayText='Facebook'></span>
			<span class='st_twitter_large' displayText='Tweet'></span>
			<span class='st_email_large' displayText='Email'></span>
		</div>
		
	</div>
	<div class="columns">
    	<div class="left_column">
			<div class="left_box">
				<a href="#" class="jaguar"><img src="/users/<?php echo $profile['info']['area_id']; ?>/profile/<?php echo $profile['info']['dealer_logo']; ?>" alt="Jaguar" /></a>
				<div class="left_content">
					<strong><?php echo str_replace("_", " ", $profile['info']['dealer_name']); ?></strong>
					<p><?php echo $profile['info']['dealer_address']; ?><br />
					<?php echo $profile['info']['dealer_city']; ?>, <?php echo $profile['info']['dealer_state']; ?> <?php echo $profile['info']['dealer_zip']; ?></p>
				 	<p><?php echo $profile['info']['phone']; ?><br />
					<?php echo $profile['info']['first'] . ' ' . $profile['info']['last']; ?> </p>
				</div>
				<a href="<?php echo $profile['info']['dealer_website']; ?>" target="_new"><img src="/images/view.gif" alt="View" /></a>
				<a href="#"><img src="/images/status.gif" alt="Status" /></a>
				<div class="msg">
					<p><?php echo $profile['info']['dealer_update']; ?></p>
				</div>
			</div>
        	<div class="widgets">
            	<img src="/images/cauley.jpg" alt="cauley" />
            </div>
            <div class="widgets">
            	<img src="/images/star.jpg" alt="star" />
            </div>
        </div>
        <div class="middle_column profileArea">
			<div class="box">
				<div class="boxArea">
					<div class="boxContent">
						<h2>Dealership Information</h2>
						<div class="leftPart">
							
							<p><strong><?php echo str_replace("_", " ", $profile['info']['dealer_name']); ?></strong></p>
							
							<p><?php echo $profile['info']['phone']; ?></p>
							<p><a href="mailto:<?php echo $profile['info']['email']; ?>" target="_new">Email this Dealer</a></p>
							<p><?php echo $profile['info']['dealer_address']; ?><br />
							<?php echo $profile['info']['dealer_city']; ?>, <?php echo $profile['info']['dealer_state']; ?> <?php echo $profile['info']['dealer_zip']; ?><br />
							</p>
							
							<ul>
								<li>- <a href="/m/dealers/<?php echo $profile['info']['area_name']; ?>">View dealership details</a></li>
								<li>- <a href="#">View a map and get directions</a></li>
								<li>- <a href="/m/dealers/<?php echo $profile['info']['area_name']; ?>/listings/">View our inventory</a></li>
								<li>- <a href="#">Finance a vehicle with us</a></li>
							</ul>
							<div class="servicesArea">
								<?php echo $profile['info']['profile_left']; ?>
							</div>
						</div>
						<div class="rightPart">
							<img src="/users/<?php echo $profile['info']['area_id']; ?>/profile/<?php echo $profile['info']['dealer_image']; ?>" alt="Place" />
							<?php echo $profile['info']['profile_right']; ?>
						</div>
						<div class="map">
						<?php
						
						$mapAddress = $profile['info']['dealer_address'] . ' ' . $profile['info']['dealer_city'] . ', ' . $profile['info']['dealer_state'] . ' ' . $profile['info']['dealer_zip'];
						
						$mapAddress = str_replace(" ", "+", $mapAddress);
						
						?>
						
						<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?saddr=3316+Empire+Dr%2C+Rochester+Hills%2C+MI+48309"></iframe>
						
							
							
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="right_column">
        	<div class="widgets">
				<div class="formArea">
					<h2>Email This Seller</h2>
					<ul>
						<li><input type="text" value="First Name" /></li>
						<li><input type="text" value="Last Name" /></li>
						<li><input type="text" value="Email" /></li>
						<li><input type="text" value="Phone Number" /></li>
						<li><textarea rows="12" cols="10">Message</textarea></li>
						
						<li><input type="submit" class="submit" value = "Send Info"/></li>
					</ul>
				</div>
				
            </div>                            
        </div>
    	<div class="clear_both"></div>
    </div>
</div>
	
	
	
</div>