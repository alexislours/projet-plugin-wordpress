<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$option_name = 'mapbox_iw_token';
delete_option($option_name);

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mapbox_iw_settings");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mapbox_iw_data");