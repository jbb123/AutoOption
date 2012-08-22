jQuery.noConflict();
jQuery(document).ready(function($){
	$('.top_right_top ul.dropdown li').click(function(){
		$('.top_right_top ul.dropdown li ul').css('display', 'block');									 
	});
	/*Shadowbox.init({ skipSetup: true }); Shadowbox.setup();*/
})