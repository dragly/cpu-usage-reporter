<?php
// STATES: running, stopped, finished, failed

require_once('../../../wp-load.php');

if( isset($_GET['runid']) ) {
    global $wpdb;
    
    $state = $_GET["state"];
    $runid = $_GET["runid"];
    
    $table_name = $wpdb->prefix . "run_statistics_runs";
    
    $rows_affected = $wpdb->update( $table_name, // table
                                    array( 'state' => $state), // values
                                    array( 'runid' => $runid) // where
                                   );
    if($rows_affected) {
        print json_encode(array("runid" => $runid));
    } else {
        print json_encode(array("runid" => $runid));
        $wpdb->print_error();
    }
}
?> 
