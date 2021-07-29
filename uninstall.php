<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$option_name = 'mapbox_iw_token';
delete_option($option_name);
