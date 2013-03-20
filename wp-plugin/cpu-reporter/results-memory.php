<?php
require_once('../../../wp-load.php');

global $wpdb;
$table_name = $wpdb->prefix . "cpu_reporter_submits";
$timeLimit = 600;
if(isset($_GET["timeLimit"])) {
    $timeLimit = $_GET["timeLimit"];
}
$results = $wpdb->get_results( $wpdb->prepare("SELECT *, (TIME_TO_SEC(TIMEDIFF(time, NOW()))) as time_difference FROM $table_name WHERE TIME_TO_SEC(TIMEDIFF(time, NOW())) > -$timeLimit  ORDER BY time ASC;" ) );
#print "SELECT time, user, usage FROM $table_name ORDER BY time DESC LIMIT 500;";
$wpdb->print_error();
$returnArray = array();
$iterator = 0;
foreach($results as $result) {
    //print_r($result);
    $returnArray[$result->user][$iterator]["x"] = $result->time_difference;
    $returnArray[$result->user][$iterator]["y"] = $result->used_memory / ($result->available_memory + $result->used_memory);
    $returnArray[$result->user][$iterator]["is_active"] = $result->is_active;
    $iterator += 1;
}
ksort($returnArray);
//print_r($returnArray);
print json_encode($returnArray); 
?>
