<?php
// Include the config file to use the DB connection

include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/init.inc.php');

include_once ('config.inc.php');

$year = $_POST['year'];     // get the year from post - sent via ajax

$vehmakes = $cl->getmakes($year);
$output = '<option value="">-- All Makes --</option>';
while($row = mysql_fetch_row($vehmakes)){
    $output .= '<option value="'.$row[0].'">'.$row[0].'</option>/n';
};

echo $output;
/* End of file makes.php */
/* Location: ./makes.php */