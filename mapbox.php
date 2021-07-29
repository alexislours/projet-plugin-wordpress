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
/*                                Load scripts                                */
/* -------------------------------------------------------------------------- */

function add_plugin_scripts()
{
  wp_enqueue_style('style', plugins_url('css/mapbox-gl.css', __FILE__), array(), '2.3.1', 'all');
  wp_enqueue_script('script', plugins_url('js/mapbox-gl.js', __FILE__), array(), '2.3.1', false);
}
add_action('wp_enqueue_scripts', 'add_plugin_scripts');

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
}

/* -------------------------------------------------------------------------- */
/*                              Custom shortcode                              */
/* -------------------------------------------------------------------------- */

function mapbox_iw_shortcode($atts)
{
  extract(shortcode_atts(
    array(
      'id' => 'map',
      'style' => 'satellite-v9',
      'zoom' => '15',
      'lon' => '2.38961',
      'lat' => '48.84907',
      'token' => get_option('mapbox_iw_token'),
      'interactive' => 'true',
      'width' => '100%',
      'height' => '400px',
      'marker' => false,
      'color' => '#f00',
      'mklon' => false,
      'mklat' => false,
    ),
    $atts
  ));
  $res = "<div id='" . $id . "' style='width: " . $width . "; height: " . $height . ";'></div>\n"
    . "<script>\n"
    . "mapboxgl.accessToken = '" . $token . "';\n"
    . "var map = new mapboxgl.Map({\n"
    . "container: '" . $id . "',\n"
    . "style: 'mapbox://styles/mapbox/" . $style . "',\n"
    . "center: ['" . $lon . "','" . $lat . "'],\n"
    . "zoom: '" . $zoom . "',\n"
    . "interactive: " . $interactive
    . "\n})\n";
  if ($marker == 'true') {
    $res .= "var marker = new mapboxgl.Marker({\n"
      . "color: '" . $color . "',\n"
      . "})\n";
    if ($mklon && $mklat) {
      $res .= ".setLngLat([" . $mklat . "," . $mklon . "])\n";
    } else {
      $res .= ".setLngLat([" . $lon . "," . $lat . "])\n";
    }
    $res .= ".addTo(" . $id . ");\n";
  }
  $res .= "</script>";

  return $res;
}
add_shortcode('mapbox', 'mapbox_iw_shortcode');

/* -------------------------------------------------------------------------- */
/*                                Custom Widget                               */
/* -------------------------------------------------------------------------- */

class mapbox_iw_Widget extends WP_Widget
{

  function __construct()
  {

    $widget_options = array(
      'classname' => 'mapbox_iw_widget',
      'description' => 'Add a customizable map.'
    );

    parent::__construct('mapbox_iw_widget', 'Mapbox', $widget_options);
  }
  function form($instance)
  {
    $id = !empty($instance['id']) ? $instance['id'] : 'widget';
    $style = !empty($instance['style']) ? $instance['style'] : 'satellite-v9';
    $zoom = !empty($instance['zoom']) ? $instance['zoom'] : '15';
    $lon = !empty($instance['lon']) ? $instance['lon'] : '2.38961';
    $lat = !empty($instance['lat']) ? $instance['lat'] : '48.84907';
    $interactive = !empty($instance['interactive']) ? $instance['interactive'] : 'true';
    $width = !empty($instance['width']) ? $instance['width'] : '100%';
    $height = !empty($instance['height']) ? $instance['height'] : '400px';
    $marker = !empty($instance['marker']) ? $instance['marker'] : 'false';
    $color = !empty($instance['color']) ? $instance['color'] : '#f00';
    $mklon = !empty($instance['mklon']) ? $instance['mklon'] : false;
    $mklat = !empty($instance['mklat']) ? $instance['mklat'] : false;
  ?>

    <p>
      <label for="<?php echo $this->get_field_id('id'); ?>">ID:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" value="<?php echo esc_attr($id); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('style'); ?>">Style:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" value="<?php echo esc_attr($style); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('zoom'); ?>">Zoom level:</label>
      <input class="widefat" type="number" min="0" max="23" id="<?php echo $this->get_field_id('zoom'); ?>" name="<?php echo $this->get_field_name('zoom'); ?>" value="<?php echo esc_attr($zoom); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('lon'); ?>">Longitude:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('lon'); ?>" name="<?php echo $this->get_field_name('lon'); ?>" value="<?php echo esc_attr($lon); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('lat'); ?>">Latitude:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('lat'); ?>" name="<?php echo $this->get_field_name('lat'); ?>" value="<?php echo esc_attr($lat); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('interactive'); ?>">Interactive:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('interactive'); ?>" name="<?php echo $this->get_field_name('interactive'); ?>" value="<?php echo esc_attr($interactive); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('width'); ?>">Width:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo esc_attr($width); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('height'); ?>">Height:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo esc_attr($height); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('marker'); ?>">Marker:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('marker'); ?>" name="<?php echo $this->get_field_name('marker'); ?>" value="<?php echo esc_attr($marker); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('color'); ?>">Marker color:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>" value="<?php echo esc_attr($color); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('mklon'); ?>">Marker longitude:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('mklon'); ?>" name="<?php echo $this->get_field_name('mklon'); ?>" value="<?php echo esc_attr($mklon); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('mklat'); ?>">Marker latitude:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('mklat'); ?>" name="<?php echo $this->get_field_name('mklat'); ?>" value="<?php echo esc_attr($mklat); ?>" />
    </p>
    <p>
  <?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['id'] = strip_tags($new_instance['id']);
    $instance['style'] = strip_tags($new_instance['style']);
    $instance['zoom'] = strip_tags($new_instance['zoom']);
    $instance['lon'] = strip_tags($new_instance['lon']);
    $instance['lat'] = strip_tags($new_instance['lat']);
    $instance['interactive'] = strip_tags($new_instance['interactive']);
    $instance['width'] = strip_tags($new_instance['width']);
    $instance['height'] = strip_tags($new_instance['height']);
    $instance['marker'] = strip_tags($new_instance['marker']);
    $instance['color'] = strip_tags($new_instance['color']);
    $instance['mklon'] = strip_tags($new_instance['mklon']);
    $instance['mklat'] = strip_tags($new_instance['mklat']);
    return $instance;
  }

  function widget($args, $instance)
  {

    $token = get_option('mapbox_iw_token');
    $id = $instance['id'];
    $style = $instance['style'];
    $zoom = $instance['zoom'];
    $lon = $instance['lon'];
    $lat = $instance['lat'];
    $interactive = $instance['interactive'];
    $width = $instance['width'];
    $height = $instance['height'];
    $marker = $instance['marker'];
    $color = $instance['color'];
    $mklon = $instance['mklon'];
    $mklat = $instance['mklat'];


    $res = "<div id='" . $id . "' style='width: " . $width . "; height: " . $height . ";'></div>\n"
      . "<script>\n"
      . "mapboxgl.accessToken = '" . $token . "';\n"
      . "var map = new mapboxgl.Map({\n"
      . "container: '". $id ."',\n"
      . "style: 'mapbox://styles/mapbox/" . $style . "',\n"
      . "center: ['" . $lon . "','" . $lat . "'],\n"
      . "zoom: '" . $zoom . "',\n"
      . "interactive: " . $interactive
      . "\n})\n";
    if ($marker == 'true') {
      $res .= "var marker = new mapboxgl.Marker({\n"
        . "color: '" . $color . "',\n"
        . "})\n";
      if ($mklon && $mklat) {
        $res .= ".setLngLat([" . $mklat . "," . $mklon . "])\n";
      } else {
        $res .= ".setLngLat([" . $lon . "," . $lat . "])\n";
      }
      $res .= ".addTo(" . $id . ");\n";
    }
    $res .= "</script>";
    echo $res;
  }
}

function mapbox_iw_register_widget()
{

  register_widget('mapbox_iw_Widget');
}
add_action('widgets_init', 'mapbox_iw_register_widget');
