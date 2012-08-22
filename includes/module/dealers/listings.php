<?php

include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

$searchVars = 0;
if ($_REQUEST['search_id'])
{
$searchVars = $dealer->getSearch($_REQUEST['search_id'], $_SESSION['user_id']);	
}

$vars = ($searchVars) ? unserialize($searchVars['details']) : $_POST;


$dealerName = $path_split[3];

$page_size  = ($_REQUEST['perpage']) ? $_REQUEST['perpage'] : 10;
$page = array_key_exists('page', $_REQUEST) ? $_REQUEST['page'] : 1;


if ($_POST['page_action'] == "Previous")
{
	$page = $page - 1;
}
if ($_POST['page_action'] == "Next")
{
	
	$page = $page + 1;
}
$vars['page'] = $page;
$page_start = ($page - 1) * $page_size;



$profile = $dealer->getDealer($dealerName, $page_start, $page_size);


$ppArr = array();
$ppArr['10'] = 10;
$ppArr['25'] = 25;
$ppArr['50'] = 50;
$ppArr['100'] = 100;


$_SESSION['compVehicles'] = (count($_SESSION['compVehicles'])) ? $_SESSION['compVehicles'] : array();

if ($_POST['action'] == "mod_compare")
{
	$vin = str_replace("c_", "", $_POST['vin']);
	
	$keyFind = array_search($vin, $_SESSION['compVehicles']);
	
	if ($keyFind === false)
	{
		array_push($_SESSION['compVehicles'], $vin);
	}
	else
	{
		unset($_SESSION['compVehicles'][$keyFind]); 
	}
}

?>

<script>
	
	function compare_vehicle(vin) 
	{
		jQuery.post('/m/dealers/<?php echo $profile['info']['dealer_name']; ?>/listings/', { vin: vin, action: 'mod_compare'});
	}
	
</script>

<div class="contnets">
	<div class="inner_wrapper">
	
	<?php include_once('header.php'); ?>
	
		<div class="headingArea">
			<div class="rightInfo">
			<form method="post" action="/m/account/savesearch" name="savesearchform">
			<input type="hidden" name="grant" value="w">
			<input type="hidden" name="dealer_id" value="<?php echo $profile['info']['area_id']; ?>">
				<?php
				foreach ($vars as $k => $v)
				{
				?>
					<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>">
				<?php	
				}
				?>
				<div class="searchInfo"><a href="#" onclick="document.savesearchform.submit();"><img src="/images/save.jpg" alt="Save" /> Save this Search</a><a href="/m/search/compare/" target="_new"><img src="/images/view.jpg" alt="View" />View Comparison Results</a> </div>
			
			</form>
				<?php

		        include_once(PATH_CLASS . 'Pagination.class.php');
		        $paging = new Pagination($page, $profile['listings'][0]['total_pages'], 10, 'pagination', 'page');
		        echo '<div class="pages">' . $paging->getPages(true, true, false) . '</div>';
		        ?>
				<form method="post" action="/m/dealers/<?php echo $profile['info']['dealer_name']; ?>/listings/" name="perpageform">
				<?php
				foreach ($vars as $k => $v)
				{
				?>
					<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>">
				<?php	
				}
				?>
				
				<div class="listing"><span>Listing per page:</span><select name="perpage" onChange="this.form.submit();"><?php echo getFormOptions($ppArr, $page_size); ?></select></div>
				
				
				<div class="prev">
				<?php
				if ($page != 1)
				{
					echo '<input type="submit" name="page_action" value="Previous">';
				}
				
				if ($page < $profile['listings'][0]['total_listings'])
				{
					echo '<input type="submit" name="page_action" value="Next">';
				}
				?>
				
				</div>
				</form>
			</div>
			<p> <?php echo str_replace("_"," ",$profile['info']['dealer_name']); ?><br>
			<?php echo $profile['listings'][0]['total_listings']; ?> listings found. </p>						
		</div>
    	<div class="columns">
        	<div class="left_column">
				<div class="left_box">
					<h2>search by zip code</h2>
					<div class="left_content">
						<div class="searchArea">
							<input type="text" />
							<input type="submit" class="go" value="" />
						</div>
					</div>
					<div class="moreSearch">
						<a href="#">Modify Search</a>
						<a href="#">New Search</a>
					</div>
				</div>
            	<div class="widgets">
                	<img src="/images/cauley.jpg" alt="cauley" />
                </div>
                <div class="widgets">
                	<img src="/images/star.jpg" alt="star" />
                </div>
            </div>
            <div class="rightSection">
			<form method="post" action="" name="compare" id="compare">
			<?php
			
			foreach ($profile['listings'] as $l)
			{
				$listPhotos = explode(";",$l['pictures']);
				
				$mainPhoto = (strlen($listPhotos[0])) ? $listPhotos[0] : 0;
				
				
			?>	
				<div class="boxSide">
					<div class="boxSection">
						<div class="boxItem">
							<div class="details">
								<h2><a href="/l/<?php echo $l['vin']; ?>"><?php echo $l['year'] . ' ' . strtoupper($l['make']) . ' ' . strtoupper($l['model']) ?></a></h2>
								<p><span><?php echo $l['exterior_color'] . ', ' . $l['body_style'] . ', ' . $l['transmission'] . ', ' . $l['engine']; ?></span></p>
								<div class="feature">
								<?php 
								if ($mainPhoto)
								{
								?>
									<img src="<?php echo $mainPhoto; ?>" alt="Feature"  width="150" height="100"/>
								<?php	
								}
								?>
								<a href="/l/<?php echo $l['vin']; ?>" class="more">More Photos</a>
								</div>
								<p><?php echo $l['seller_comments']; ?></p>
								<a href="/l/<?php echo $l['vin']; ?>" class="view-details">View more details </a>
							</div>
							<div class="productInfo">
								<span class="jaguar">
									<img src="/users/<?php echo $profile['info']['area_id']; ?>/profile/<?php echo $profile['info']['dealer_logo']; ?>" alt="Jaguar" />
								</span>
								<strong><?php echo str_replace("_", " ", $profile['info']['dealer_name']); ?> </strong>
								<p><?php echo $profile['info']['dealer_address']; ?><br />
								<?php echo $profile['info']['dealer_city']; ?>, <?php echo $profile['info']['dealer_state']; ?> <?php echo $profile['info']['dealer_zip']; ?></p>
								<strong class="phone"><?php echo $profile['info']['phone']; ?></strong>
								<a href="<?php echo $profile['info']['website_url']; ?>" target="_new"><img src="/images/view-more.gif" alt="View" /></a>
							</div>
							<div class="rating">
								
								<strong>Mileage: <?php echo number_format($l['mileage'],0); ?></strong>
								<strong class="price">$<?php echo number_format($l['price'],2); ?></strong>
								<ul>
									<li><input type="checkbox" name="c_<?php echo $l['vin']; ?>" value="1" <?php echo $vehicleChecked; ?> onClick="compare_vehicle(this.name);" /> Compare Ad</li>
								</ul>
								<b class="date">Posted : <?php echo tsDisplay($l['create_time']); ?></b>
							</div>
						</div>
					</div>
				</div>
			
			<?php
			}
			?>
			</form>
			</div>
        	<div class="clear_both"></div>
        </div>
    </div>
</div>



