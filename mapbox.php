<?php

/**
 * Plugin Name:       Wordpress Mapbox
 * Description:       Inserts a mapbox map into your wordpress site.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Alexis LOURS
 * License:           CC0-1.0
 * License URI:       https://creativecommons.org/share-your-work/public-domain/cc0/
 * Text Domain:       mapbox_iw
 */

register_activation_hook(__FILE__, 'mapbox_iw_install');

function mapbox_iw_install()
{
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  // Store Mapbox settings
  $settings_table_name = $wpdb->prefix . "mapbox_iw_settings";
  $sqlSettings = "CREATE TABLE $settings_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        token VARCHAR(100) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
      ) $charset_collate;";
  dbDelta($sqlSettings);

  $data_table_name = $wpdb->prefix . "mapbox_iw_data";
  $sqlData = "CREATE TABLE $data_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        lat FLOAT DEFAULT 0 NOT NULL,
        lon FLOAT DEFAULT 0 NOT NULL,
        tileset VARCHAR(100) DEFAULT '' NOT NULL,
        zoom TINYINT DEFAULT 0 NOT NULL,
        locked TINYINT(1) DEFAULT 0 NOT NULL,
        PRIMARY KEY  (id)
      ) $charset_collate;";
  dbDelta($sqlData);
}
