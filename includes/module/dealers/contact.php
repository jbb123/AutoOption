<?php

if ($_POST['action'] == "add_contact")
{
	
	$sql = "
			INSERT INTO contacts (name, email, phone, comment, create_time, ip_address)
			VALUES (%s, %s, %s, %s, %d, %d)
            ";
            $db->safeQuery($sql,
            				$_POST['name'],
						    $_POST['email'],
						    $_POST['phone'],
						    $_POST['comment'],
						    TIME_NOW,
						    ip2Long($_SERVER['REMOTE_ADDR'])
						    );
	$success = 1;
	
}

?>


<div class="contnets">
	<div class="inner_wrapper">
<?php
include_once('header.php');
?>

<?php 
if ($success)
{
	echo '<center><font color="00AA00">Thank you, someone will be in touch with you shortly.</font></center>';
}

/*
 * <div class="required error">
			<p class="error">Required.</p>
 * 
 */

?>


<form action="/m/contact/index.php" method="post">
    <input type="hidden" name="action" value="add_contact" />
	<input type="hidden" name="grant" value="w" />
	
	<fieldset>
		<legend>General Information</legend>
		<div class="notes">
			<h4>Personal Information</h4>
	        <p class="last">Please enter your name and address as they are listed for your debit card, credit card, or bank account.</p>
		</div>
									
		<div class="required">
			<label for="name">Name:</label>
			<input type="text" name="name" id="name" class="inputText" size="10" maxlength="100" value="" />
		</div>
		<div class="required">
			<label for="name">Email:</label>
			<input type="text" name="email" id="email" class="inputText" size="10" maxlength="100" value="" />
		</div>
		<div class="required">
			<label for="name">Phone:</label>
			<input type="text" name="phone" id="phone" class="inputText" size="10" maxlength="100" value="" />
		</div>
		<div class="required">
			<label for="name">Comments:</label>
			<textarea name = "comments" cols="70" rows = "8"></textarea>
		</div>
		
	</fieldset>
    <fieldset>
      <div class="submit">
        <div>
          <input type="submit" class="inputSubmit" value="Submit Form" />
          <input type="submit" class="inputSubmit" value="Cancel" />
        </div>
      </div>
    </fieldset>
</form>



	
	</div>
</div>