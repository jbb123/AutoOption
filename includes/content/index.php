<?php
include_once (PATH_CLASS.'Content.class.php');

$content = New Content();

$pageContent = $content->getContent($path_split[2]);

?>

<div class="contents">
	<div class="inner_wrapper">

		<div class="left_column">
			<div class="widgets">
				<img alt="cauley" src="/images/cauley.jpg">
			</div>
			<div class="widgets">
				<img alt="star" src="/images/star.jpg">
			</div>
		</div>
		
		<div class="middle_column" style="padding: 20px 0 0 20px;">
		
<div class="box">
			<div class="boxArea">
				<div class="boxContent">
					<?php
						echo $pageContent['content'];

						if ($pageContent['content_id'] == 2)
						{
							echo '<br><br>';
							include_once('module/packages/index.php');
							
						}
						
						if ($pageContent['content_id'] == 12)
						{
							echo '<br><br>';
							include_once('module/loan/index.php');
						}
						
					?>
					
				</div>
			</div>
				</div>					
	
		</div>

	</div>
</div>






