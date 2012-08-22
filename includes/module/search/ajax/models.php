<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/init.inc.php');
// Include the config file so we can use the DB connection
include_once ('config.inc.php');

$year = $_POST['year'];     // get the year from post - sent via ajax
$make = $_POST['make'];     // get the make from post - sent via ajax

$vehmodels = $cl->getmodels($year, $make);
$output = '<option value="">-- All Models --</option>';
while($row = mysql_fetch_row($vehmodels)){
    $output .= '<option value="'.$row[0].'">'.$row[0].'</option>/n';
};

echo $output;
/* End of file models.php */
/* Location: ./models.php */