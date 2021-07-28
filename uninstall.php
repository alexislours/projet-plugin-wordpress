<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mapbox_iw_settings");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mapbox_iw_data");