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

/* -------------------------------------------------------------------------- */
/*                                Plugin setup                                */
/* -------------------------------------------------------------------------- */

register_activation_hook(__FILE__, 'mapbox_iw_install');

function mapbox_iw_install()
{
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
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

/* -------------------------------------------------------------------------- */
/*                                Settings page                               */
/* -------------------------------------------------------------------------- */

function mapbox_iw_register_settings()
{
  add_option('mapbox_iw_token', 'Your Mapbox token');
  register_setting('mapbox_iw_options_group', 'mapbox_iw_token', 'mapbox_iw_callback');
}
add_action('admin_init', 'mapbox_iw_register_settings');

function mapbox_iw_register_options_page()
{
  add_options_page('Mapbox', 'Mapbox Menu', 'manage_options', 'mapbox_iw', 'mapbox_iw_options_page');
}
add_action('admin_menu', 'mapbox_iw_register_options_page');

function mapbox_iw_options_page()
{
?>
  <div>
    <?php screen_icon(); ?>
    <h2>Mapbox settings</h2>
    <form method="post" action="options.php">
      <?php settings_fields('mapbox_iw_options_group'); ?>
      <p>Global settings for Mapbox</p>
      <table>
        <tr valign="top">
          <th scope="row"><label for="mapbox_iw_token">Token</label></th>
          <td><input type="text" id="mapbox_iw_token" name="mapbox_iw_token" value="<?php echo get_option('mapbox_iw_token'); ?>" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
} ?>