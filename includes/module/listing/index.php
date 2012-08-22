<?php
include_once (PATH_CLASS.'Dealer.class.php');

$vin = $path_split[2];

$dealer = New Dealer();

$listing = $dealer->getDealerListing($vin);


if ($_REQUEST['contact'])
{
	$success = $dealer->contactForm($listing['info']['dealer_name'], $_POST);
}
$listPhotos = explode(";",$listing['listing']['pictures']);

?>

<div class="contents">
	<div class="inner_wrapper">
	
	<?php
	if ($success)
	{
		echo 'Thank you for contacting this dealer.<br><br>';
	}
	?>
					<div class="headingArea">
						<div class="pageNav">
							<strong><a href="javascript:history.go(-1);">Back to Results</a></strong>
							
						</div>
						<h2><?php echo $listing['listing']['year'] . ' ' . $listing['listing']['make'] . ' ' . $listing['listing']['model'] . ' $' . number_format($listing['listing']['price'],2); ?> </h2>
						
						<form method="post" action="/m/account/savesearch" name="savesearchform">
						<input type="hidden" name="grant" value="w">
						<input type="hidden" name="listing_id" value="<?php echo $listing['listing']['vin']; ?>">
						<div class="links">
						
							<label><a href="#" onclick="document.savesearchform.submit();"><img src="/images/save.jpg" alt="Save" /> Save this Search</a></label><label><img src="/images/print.gif" alt="Print" /><a href="javascript:window.print()">Print This Ad</a></label><label><img src="/images/cal.gif" alt="Cal" /><a href="/c/loan" target="loan">Loan Calculator</a></label><label><input type="checkbox" /><a href="#">Compare Ad</a></label>
							<span class='st_facebook_large' displayText='Facebook'></span>
							<span class='st_twitter_large' displayText='Tweet'></span>
							<span class='st_email_large' displayText='Email'></span>
						</div>
						
						</form>
						
					</div>
                	<div class="columns">
                    	<div class="left_column">
							<div class="left_box">
								<a href="#" class="jaguar"><img src="/users/<?php echo $listing['info']['area_id']; ?>/profile/<?php echo $listing['info']['dealer_logo']; ?>" alt="Jaguar" /></a>
								<div class="left_content">
									<strong><?php echo str_replace("_", " ", $listing['info']['dealer_name']); ?></strong>
									<p><?php echo $listing['info']['dealer_address']; ?><br />
									<?php echo $listing['info']['dealer_city']; ?>, <?php echo $listing['info']['dealer_state']; ?> <?php echo $listing['info']['dealer_zip']; ?></p>
									<p><?php echo $listing['info']['phone']; ?><br />
									<?php echo $listing['info']['first'] . ' ' . $listing['info']['last']; ?> </p>
								</div>
								<a href="<?php echo $listing['info']['dealer_website']; ?>" target="_new"><img src="/images/view.gif" alt="View" /></a>
								<a href="#"><img src="/images/status.gif" alt="Status" /></a>
								<div class="msg">
									<p><?php echo $listing['info']['dealer_update']; ?></p>
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
									<?php
									if (strlen($listPhotos[0]))
									{
									?>
									<span class="view"><img src="<?php echo $listPhotos[0]; ?>" width="549" height="377" alt="Car View" /></span>
									<?php	
									}
									?>
										
									</div>
								</div>
							</div>
							<div class="box">
								<div class="boxArea">
									<div class="boxContent">
										<div class="slideNo">
										<a href="javascript:" class="prev2"><img src="/images/prev.gif" alt="Prev" /></a>
										<a href="javascript:" class="next2"><img src="/images/next.gif" alt="Next" /></a>
									</div>
									<h2>Gallery</h2>
									<div class="btmslide2 btm">
										<dl class="gallery">
											<?php
											
											foreach ($listPhotos as $p)
											{
											?>
												<dd><img src="<?php echo $p; ?>" class="iGallery" width="123" /></dd>
											<?php	
											}
											?>
											
											
										</dl>
									</div>
									 <script type="text/javascript">
										$(function() {
											$('.btmslide2').scrollable({clickable:false,size:4,items:'.gallery',prev:'.prev2',next:'.next2'}).circular();
										});
										
										  $('img.iGallery').live('click', function(){ 
										  	var imgSource = $(this).attr('src');
										  	$('span.view').html("<img src='" + imgSource + "' width='549' height='377'>")
									      });

									</script>
									</div>
								</div>
							</div>
							<div class="box">
								<div class="boxArea">
									<div class="boxContent">
										<h2>Primary information about this vehicle:</h2>
										<div class="prInfo">
											<div class="tableSide">
											<div class="tableCon">
												<div class="table">
													<table width="322" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<th width="104">Listing ID</th><td width="174">AT-12E149FD</td>
														</tr>
														<tr>
															<th>Price</th><td>$ <?php echo number_format($listing['listing']['price'],2); ?></td>
														</tr>
														<tr>
															<th>Mileage</th><td><?php echo number_format($listing['listing']['mileage'],2); ?></td>
														</tr>
														<tr>
															<th>Body Style</th><td><?php echo $listing['listing']['body_style']; ?></td>
														</tr>
														<tr>
															<th>Exterior Color</th><td><?php echo $listing['listing']['exterior_color']; ?></td>
														</tr>
														<tr>
															<th>Interior Color</th><td><?php echo $listing['listing']['interior_color']; ?></td>
														</tr>
														<tr>
															<th>Engine</th><td><?php echo $listing['listing']['engine']; ?></td>
														</tr>
														<tr>
															<th>Transmission</th><td><?php echo $listing['listing']['transmission']; ?></td>
														</tr>
														<tr>
															<th>Fuel Type</th><td><?php echo $listing['listing']['fuel_type']; ?></td>
														</tr>
														<tr>
															<th>Doors</th><td><?php echo $listing['listing']['doors']; ?></td>
														</tr>
														<tr>
															<th>VIN</th><td><?php echo $listing['listing']['vin']; ?></td>
														</tr>
													</table>
												</div>
											</div>
											</div>
											<div class="rightArea">
												<img src="/images/car-icon.jpg" alt="Car Loan" />
												<label><a href="/c/fincance/">Get a Loan</a></label>
												
												<a href="http://www.carfax.com/cfm/general_check.cfm?BannerName=4&AffiliateID=5976&partner=kow_a&vin=<?php echo $listing['listing']['vin']; ?>" target="_new"><img src="/images/show-me.jpg" alt="Show Me The CarFax" /></a>
												<label>View the Free CARFAX Report <br />for this <?php echo $listing['listing']['make'] . ' ' . $listing['listing']['model']; ?></label>
											</div>
											
										</div>
										<div class="info">
											<h2>Vehicle Description</h2>
											<h3>Seller's Description and Comments</h3>
											<p><?php echo $listing['listing']['seller_comments']; ?></p>
											<h3>Disclaimer</h3>
											<p>Sales Tax, Title, License Fee, Registration Fee, Dealer Documentary Fee, Finance Charges, Emission Testing Fees and Compliance Fees are additional to the advertised price. All vehicles that qualify for select certified program will have the "Select Certified" logo listed. Prices do not reflect select certified coverage. See dealer for details.</p>
										</div>
									</div>
								</div>
							</div>
							<div class="box">
								<div class="boxArea">
									<div class="boxContent">
										<h2>Dealership Information</h2>
										<div class="leftPart">
											
											<p><strong><?php echo str_replace("_", " ", $listing['info']['dealer_name']); ?></strong></p>
											<a href="#" class="jaguar"><img src="/users/<?php echo $listing['info']['area_id']; ?>/profile/<?php echo $listing['info']['dealer_logo']; ?>" alt="Jaguar" /></a>
											<p><?php echo $listing['info']['phone']; ?></p>
											<p><a href="mailto:<?php echo $listing['info']['email']; ?>" target="_new">Email this Dealer</a></p>
											<p><?php echo $listing['info']['dealer_address']; ?><br />
											<?php echo $listing['info']['dealer_city']; ?>, <?php echo $listing['info']['dealer_state']; ?> <?php echo $listing['info']['dealer_zip']; ?><br />
											</p>
											
											<ul>
												<li>- <a href="/m/dealers/<?php echo $listing['info']['area_name']; ?>">View dealership details</a></li>
												<li>- <a href="#">View a map and get directions</a></li>
												<li>- <a href="/m/dealers/<?php echo $listing['info']['area_name']; ?>/listings">View our inventory</a></li>
												<li>- <a href="#">Finance a vehicle with us</a></li>
											</ul>
											<div class="servicesArea">
												<?php echo $listing['info']['profile_left']; ?>
											</div>
										</div>
										<div class="rightPart">
											<img src="/users/<?php echo $listing['info']['area_id']; ?>/profile/<?php echo $listing['info']['dealer_image']; ?>" alt="Place" />
											<?php echo $listing['info']['profile_right']; ?>
										</div>
										<div class="map">
											<img src="/images/map.jpg" alt="Map" />
											
										</div>
										
										
									</div>
								</div>
							</div>
						</div>
                        <div class="right_column">
                        	<div class="widgets">
								<div class="formArea">
								<form method="post" action="/l/<?php echo $listing['listing']['vin']; ?>/?contact=1">
									<h2>Email This Seller</h2>
									<ul>
										<li><input type="text" name="first_name" value="First Name" /></li>
										<li><input type="text" name="last_name" value="Last Name" /></li>
										<li><input type="text" name="email" value="Email" /></li>
										<li><input type="text" name="phone" value="Phone Number" /></li>
										<li><textarea rows="12" name="msg" cols="10">Message</textarea></li>
										<li><input type="submit" class="submit" value="Send Info" /></li>
									</ul>
								</form>
								</div>
								<?php
								if ($listing['listing']['youtube_video_id'])
								{
								?>
									<div class="youtube">
										<h3>Youtube Spot
											<strong>made by dealer.</strong>
										</h3>
										<div class="videoArea">
										Youtube Code Here <?php echo $listing['listing']['youtube_video_id']; ?>
										</div>
									</div>
								<?php
								}
								?>
                            </div>                            
                        </div>
                    	<div class="clear_both"></div>
                    </div>
                </div>
	
	
	
</div>






