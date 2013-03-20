<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Raphaël · Analytics</title>
        <link rel="stylesheet" href="demo.css" type="text/css" media="screen">
        <link rel="stylesheet" href="demo-print.css" type="text/css" media="print">
        <script src="raphael.js"></script>
        <script src="popup.js"></script>
        <script src="jquery.js"></script>
        <script src="analytics.js"></script>
        <style type="text/css" media="screen">
            #holder {
                height: 250px;
                margin: -125px 0 0 -400px;
                width: 800px;
            }
        </style>
    </head>
    <body>
        <?php 
        require_once('../../../wp-load.php');
        if( isset($_GET['user']) ) {
            global $wpdb;
            $user = $_GET['user'];
            $table_name = $wpdb->prefix . "cpu_reporter_submits";
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user = '$user' ORDER BY time DESC LIMIT 15;" ) );
            //print "SELECT * FROM $table_name WHERE user = '$user';";
            $wpdb->print_error();
        }
        ?>
        <table id="data">
            <tfoot>
                <tr>
                <th>00:00</th>
                <?php
                $results = array_reverse($results);
                foreach($results as $result) {
                    print "<th>" . date("H:i", strtotime($result->time)) . "</th>";
                }
                ?>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                <td>100</td>
                <?php
                foreach($results as $result) {
                    print "<td>" . $result->usage . "</td>";
                }
                ?>
                </tr>
            </tbody>
        </table>
        <div id="holder"></div>
        <p id="copy">Demo of <a href="http://raphaeljs.com/">Raphaël</a>—JavaScript Vector Library</p>
    </body>
</html>
