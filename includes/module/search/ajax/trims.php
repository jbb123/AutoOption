<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/init.inc.php');

// Include the config file so we can use the DB connection
include_once ('config.inc.php');

$year = $_POST['year'];     // get the year from post - sent via ajax
$make = $_POST['make'];     // get the make from post - sent via ajax
$model = $_POST['model'];   // get the model from post - sent via ajax

$vehstyles = $cl->getstyles($year, $make, $model);
$output = '<option value="">-- All Trims --</option>';
while($row = mysql_fetch_row($vehstyles)){
    $output .= '<option value="'.$row[0].'">'.$row[0].'</option>/n';
};

echo $output;
/* End of file trims.php */
/* Location: ./trims.php */