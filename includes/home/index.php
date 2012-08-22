<?php

include_once (PATH_CLASS.'Dealer.class.php');

$dealer = New Dealer();

$makes = $dealer->getMakes();

?>


<div class="contnets">
			<div class="inner_wrapper">
				<div class="columns">
					<div class="left_column">
						<div class="widgets">
							<img src="images/cauley.jpg" alt="cauley" />
						</div>
						<div class="widgets">
							<img src="images/star.jpg" alt="star" />
						</div>
					</div>
					<div class="middle_column">
						<div class="box topBox">
							<div class="boxArea">
								<div class="boxContent">
									<div class="slideNo">
										<a href="javascript:" class="previous"><img src="images/prev.gif" alt="Prev" /></a>
										<a href="javascript:" class="next"><img src="images/next.gif" alt="Next" /></a>
									</div>
									<h2>Featured Ads</h2>
									<div class="btmslide">
										<dl class="gallery">
											<dd><img src="images/sample.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
											<dd><img src="images/sample2.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
											<dd><img src="images/sample3.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
											<dd><img src="images/sample4.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
										</dl>
									</div>
									 <script type="text/javascript">
										$(function() {
											$('.btmslide').scrollable({clickable:false,size:4,items:'.gallery',prev:'.previous',next:'.next'}).circular();
										});
									</script>
								</div>
							</div>
						</div>
						<div class="box">
							<div class="boxArea">
								<div class="boxContent">
									<div class="slideNo">
										<a href="javascript:" class="prev2"><img src="images/prev.gif" alt="Prev" /></a>
										<a href="javascript:" class="next2"><img src="images/next.gif" alt="Next" /></a>
									</div>
									<h2>Featured Leases</h2>
									<div class="btmslide2">
										<dl class="gallery">
											<dd><img src="images/sample.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
											<dd><img src="images/sample2.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
											<dd><img src="images/sample3.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
											<dd><img src="images/sample4.png" alt="Sample" />
												<p>1957 AMG M35A2
												<strong>$13,000.00 </strong>
												</p>
											</dd>
										</dl>
									</div>
									 <script type="text/javascript">
										$(function() {
											$('.btmslide2').scrollable({clickable:false,size:4,items:'.gallery',prev:'.prev2',next:'.next2'}).circular();
										});
									</script>
								</div>
							</div>
						</div>
						<div class="box">
							<div class="boxArea">
								<div class="boxContent">
									<h2>Cars By Make</h2>
									<div class="tableArea">
										<div class="tablePart">
											<div class="tableContent">
												<table width="560" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td width="135"></td>
													<td width="152"></td>
													<td width="146"></td>
													<td width="110"></td>
												  </tr>
												  <tr>
												  <?php
												  $perRow = 4;
												  $rowCnt = 0;
												  foreach ($makes as $m)
												  {
												  ?>
												  
												  <td><?php echo '- <a href="/m/search/results/?make='.$m['make'].'">' . $m['make'] . '</a> (' . $m['count']. ')'; ?></td>
													  <?php
													  $rowCnt++;
													  if ($rowCnt == $perRow)
													  {
													  
													  ?>
													  </tr><tr>
													  <?php
													  $rowCnt = 0;	
													  }
												  
												  
												  }
												  
												  ?>
												  
												 
													
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<div class="right_column">
						<div class="widgets">
							<img src="images/cauley.jpg" alt="cauley" />
						</div>						
					</div>
					<div class="clear_both"></div>
				</div>
			</div>
		</div>


