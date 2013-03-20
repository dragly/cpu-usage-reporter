<?php
/*
Plugin Name: CPU Reporter
Plugin URI: http://dragly.org/source
Description: Stores CPU usage for pretty plotting
Version: 0.0.1
Author: Svenn-Arne Dragly
Author URI: http://dragly.org/
License: GNU GPLv3
*/
include("functions.php");
global $cpu_reporter_db_version;
$cpu_reporter_db_version = "0.0.1";

function cpu_reporter_install () {
    global $wpdb;
    global $cpu_reporter_db_version;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->prefix . "cpu_reporter_submits";
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `user` varchar(255) NOT NULL,
            `usage` DOUBLE NOT NULL,
            `is_active` mediumint(1),
	    `available_memory` bigint(20) DEFAULT '0',
	    `used_memory` bigint(20) DEFAULT '0',
            UNIQUE KEY `id` (`id`)
            );";
            
    dbDelta($sql);
    $table_name = $wpdb->prefix . "cpu_reporter_latest";
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `user` varchar(255) NOT NULL,
            `usage` DOUBLE NOT NULL,
            `is_active` mediumint(1),
	    `available_memory` bigint(20) DEFAULT '0',
	    `used_memory` bigint(20) DEFAULT '0',
            UNIQUE KEY `id` (`id`)
            );";

    dbDelta($sql);
    add_option("cpu_reporter_db_version", $cpu_reporter_db_version);
}

register_activation_hook(__FILE__,'cpu_reporter_install');

function my_scripts_method() {
    wp_enqueue_script('flot', plugins_url() . '/cpu-reporter/jquery.flot.js');
    wp_enqueue_script('flotpie', plugins_url() . '/cpu-reporter/jquery.flot.pie.min.js');
//     wp_enqueue_script('jquerieie', plugins_url() . '/cpu-reporter/jquery.js');
}
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
?>