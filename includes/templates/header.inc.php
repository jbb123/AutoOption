<?php ob_start(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Automate</title>
<link href="/includes/styles/styles.css" rel="stylesheet" type="text/css" />
<link href="/includes/styles/forms.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/includes/styles/nivo-slider.css" type="text/css" media="screen" />
<script type="text/javascript" src="/includes/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/includes/js/custom.js"></script>
<script type="text/javascript" language="javascript" src="/includes/js/jquery.js"></script>
<script type="text/javascript" src="/includes/js/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript" src="/includes/js/tools.scrollable.js"></script>
<script type="text/javascript" src="/includes/js/tools.scrollable.circular.js"></script>
<script type="text/javascript" src="/includes/js/tools.scrollable.autoscroll.js"></script>

<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "ur-47a756f8-a1cb-f7d-ce7f-85e65194447"}); </script>

<script type="text/javascript">
$(function() {
    $('#slider').nivoSlider({
       effect: 'fade'
   });
});
</script>

</head>

<body>


<?php

include_once (PATH_INCLUDE . '/module/search/ajax/config.inc.php');

$priceArr = array();

$priceArr['1:10000'] = "Under $10,000";
$priceArr['10000:15000'] = "$10,000 to $15,000";
$priceArr['15000:20000'] = "15,000 to 20,000";
$priceArr['20000:30000'] = "$20,000 to $30,000";
$priceArr['30000:40000'] = "$30,000 to $40,000";
$priceArr['40000:50000'] = "$40,000 to $50,000";
$priceArr['50000:5000000'] = "Over $50,000";

if ($path_split[2])
{
?>
<div class="sub">
<?php
}
?>

	<div class="outer_wrapper">
    	<div class="top">
        	<div class="inner_wrapper">
            	<a href="/" title="automate"><img src="/images/logo.png" class="logo" alt="Automate" /></a>
                <div class="top_right">
                	<div class="top_right_top">
                    	<ul class="dropdown">
                        	<li><a href="#" title="twitter"><img src="/images/t.png" alt="t" /></a></li>
                            <li><a href="#" title="facebook"><img src="/images/f.png" alt="f" /></a></li>
                            <li>
                            <?php
                            	if ($_SESSION['user_id'])
                            	{
                            		echo '<a href="/m/account/logout"><img src="/images/logout-button.png" alt="login" /></a>';
                            	}
                            	else
                            	{
                            	echo '<a href="/m/account"><img src="/images/login-button.png" alt="login" border="0"/></a> <a href="/m/account"><img src="/images/register-button.png" alt="login" /></a>';	
                            	}
                            ?>	
                            </li>
                        </ul>
                    </div>
                    <div class="top_right_bottom">
                    	<div class="call_now">Call Now : <span>705-555-1212</span></div>
                        <ul class="page_menus">
                        	<li><a href="/">Home</a></li>
                            <li><a href="/m/contact">Contact Us</a></li>
                            <li><a href="/c/blog">Blog</a></li>
                            <li><a href="/c/help">Need Help?</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $sellLink = ($_SESSION['user_id']) ? '/m/account/dashboard' : '/c/packages/';
    ?>
    
    <div class="main_contents">
    	<div class="header">
        	<div class="inner_wrapper">
            	<div class="topmenu">
                	<ul class="top_menus">
                    	<li><a href="/m/search">Search Inventory</a></li>
                        <li><a href="/c/packages">Available Packages</a></li>
                        <li><a href="<?php echo $sellLink; ?>">Sell Your Car</a></li>
                        <li><a href="/c/advertise">Advertise</a></li>
                        <li><a href="/c/finance">Finance</a></li>
                        <li><a href="/c/insurance">Auto Insurance</a></li>
                        <li><a href="/m/dealers">Search Dealers</a></li>
                        <li><a href="/c/magazine">Magazine</a></li>
					</ul>
                </div>
                <div class="banners">
                	<div class="banner_contents">
                    	<div class="search_menu">
                        	<ul class="search_tab">
                            	<li><a href="/m/account/dashboard/">Saved Ads</a></li>
                                <li><a href="/m/account/dashboard/">Saved Searches</a></li>
                                <li><a href="/c/sell"><img class="list_car" src="/images/list-your-car.png" alt=" " /></a></li>
                            </ul>
                        </div>
                    <?php 
                    if (!$path_split[2])
                    { 
                    ?>
					<div class="sliding_banner">
						<div id="slider" class="nivoSlider">
							<img src="/images/slide.png" alt="Slide" title="#htmlcaption" />
							<img src="/images/slide2.png" alt="Slide" title="#htmlcaption2" />
							<img src="/images/slide.png" alt="Slide" title="#htmlcaption" />
							<img src="/images/slide2.png" alt="Slide" title="#htmlcaption2" />
						</div>
						<div id="htmlcaption" class="nivo-html-caption">
							<h2>Special low rates
								<strong>Available here</strong>
							</h2>
							<ul>
								<li><a href="#">Special low rates available|									
								here.</a></li>
								<li>Huge selection of cars,<br /> 
								locations, agencies.</li>
								<li>Easy searching. Easy<br />
								booking.</li>
							</ul>
						</div>
						<div id="htmlcaption2" class="nivo-html-caption">
							 <h2>Special low rates
								<strong>Available here</strong>
							</h2>
							<ul>
								<li><a href="#">Special low rates available|									
								here.</a></li>
							</ul>
						</div>
					</div>
					<div class="search_form">	
					<form action="/m/search/results/" id="cardbform" method="post" accept-charset="utf-8">					
						<dl>
							<dd><h2>Find your car</h2></dd>
							<dd><label>Year</label>
								<select name="year" id="year" size="1">
									<option value="">-- All Years --</option> 
								    <?php
								    //Lets get the years
								     $vehyears = $cl->getyears();
								     while($row = mysql_fetch_row($vehyears)){
								     echo '<option value="'.$row[0].'">'.$row[0].'</option>/n';
								     };
								     ?>
								</select>
							</dd>
							<dd><label>Make Search</label>
								<select name="make" id="make" size="1">
			                        <option value="">-- All Makes --</option> 
			                    </select>
							</dd>
							<dd><label>Model Search</label>
								<select name="model" id="model" size="1">
			                        <option value="">-- All Models --</option> 
			                    </select>
							</dd>
							<dd><label>Price Range</label><select name="price"><?php echo getFormOptions($priceArr, 0); ?></select></dd>
							<dd><label>City Name</label><input type="text" name="city" /></dd>
							<dd class="button">
								<input class="normalsearch" type="image" src="/images/search-button.png" />
								<a href="/m/search"><image src="/images/advance-searc.png" border="0"></a>
							 </dd>
						</dl>
					</form>	
					</div>
                        
				<?php
				}
                ?>
                        
                        
                        
					</div>
                </div>
            </div>
        </div>