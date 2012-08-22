<?php

include_once('/includes/init.inc.php');


/**
 * CarList - Configuration file
 * 
 * CarList - Database and script to show Year, Make, Model and Trim on your website.
 * Can be used as a search tool, adding a vehicle to a site and more.
 *
 * @package		CarList
 * @author		Ideal Web Solutions
 * @copyright	Copyright (c) 2011, Ideal Web Solutions, LLC.
 * @version		Version 1.1
 * 
 */

$dbname  = 'auto';  // Set the database name
$dbhost  = 'localhost';             // Set the database host
$dbuser  = 'root';      		// Set the database username
$dbpass  = 'jL4unchp4d';        		// Set the database password
$dbtable = 'makesmodels';           // Set the database table name
$dbtblvt = 'bodystyles';			// Vehicle body styles

define('BASEPATH', '/');            // Not used at the moment

// Include the CarList.class.php file
include_once(PATH_CLASS.'CarList.class.php');

// Let's create an instance of the CarList to use later
$cl = new CarList($dbhost,$dbname,$dbuser,$dbpass,$dbtable,$dbtblvt);

/* End of file config.php */
/* Location: ./carlistdb/include/config.php */

?>
