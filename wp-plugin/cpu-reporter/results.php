<?php
require_once('../../../wp-load.php');

global $wpdb;
$resultType = $_GET["type"];
$timeLimit = 600;
if(isset($_GET["timeLimit"])) {
    $timeLimit = $_GET["timeLimit"];
}
$latest = $_GET["latest"];
if($latest == 1) {
  $table_name = $wpdb->prefix . "cpu_reporter_latest";
} else {
  $table_name = $wpdb->prefix . "cpu_reporter_submits";
}
$results = $wpdb->get_results( $wpdb->prepare("SELECT *, (TIME_TO_SEC(TIMEDIFF(time, NOW()))) as time_difference FROM $table_name WHERE TIME_TO_SEC(TIMEDIFF(time, NOW())) > -$timeLimit  ORDER BY time ASC;" ) );
#print "SELECT time, user, usage FROM $table_name ORDER BY time DESC LIMIT 500;";
$wpdb->print_error();
$returnArray = array();
$iterator = 0;
$unused = 0;
foreach($results as $result) {
    //print_r($result);
    if($latest == 1) {
	$returnArray[$result->user][$iterator]["x"] = 1;
    } else {
	$returnArray[$result->user][$iterator]["x"] = $result->time_difference;
    }
    $y = 0;
    if($resultType == "cpu") {
	$y = $result->usage;
    } else {
	if($result->available_memory + $result->used_memory == 0) {
	    $y = 0;
	} else {
	    $y = $result->used_memory / ($result->available_memory + $result->used_memory);
	}
	$y *= 100;
	$unused += 100 - $y;
    }
    $returnArray[$result->user][$iterator]["y"] = $y;
    $returnArray[$result->user][$iterator]["is_active"] = $result->is_active;
    $iterator += 1;
}
if($latest == 1 && $resultType == "memory") {
    $returnArray["Unused"][0]["x"] = 1;
    $returnArray["Unused"][0]["y"] = $unused;
    $returnArray["Unused"][0]["is_active"] = 0;
}
ksort($returnArray);
//print_r($returnArray);
print json_encode($returnArray); 
?>
