<?php
require_once('../../../wp-load.php');

if( isset($_GET['user']) ) {
    //Reads the posted values
    $user = $_GET["user"];
    $usage = $_GET["usage"];
    $availableMemory = 0;
    $usedMemory = 0;
    if(isset($_GET["available_memory"]) && isset($_GET["used_memory"])) {
      $availableMemory = $_GET["available_memory"];
      $usedMemory = $_GET["used_memory"];
    }
    $isActive = 0;
    if(isset($_GET["is_active"])) {
        $isActive = $_GET["is_active"];
    }

    global $wpdb;
    $runid = uniqid("", true);
    $wpdb->show_errors();
    $table_name = $wpdb->prefix . "cpu_reporter_submits";
    $data = array( 'user' => $user,
		    'usage' => $usage,
		    'available_memory' => $availableMemory,
		    'used_memory' => $usedMemory,
		    'is_active' => $isActive,
		    'time' => current_time("mysql"));
    $rows_affected = $wpdb->insert( $table_name, 
                                    $data
                                   );
    $table_name = $wpdb->prefix . "cpu_reporter_latest";
    $results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE user = '$user';" ) );
    if(count($results) == 0) {
	$rows_affected = $wpdb->insert( $table_name, 
					$data
				      );
    } else {
	$rows_affected = $wpdb->update( $table_name, 
					$data,
					array('user' => $user)
				      );
    }
    // Delete old results
    $wpdb->query( 
	$wpdb->prepare( "DELETE FROM `wp_cpu_reporter_submits` WHERE TIMESTAMPDIFF(DAY, time, NOW()) > 0")
    );
    if($rows_affected) {
        print json_encode(array("user" => $user, "usage" => $usage, "is_active" => $isActive));
    } else {
        $wpdb->print_error();
    }
}
?> 
