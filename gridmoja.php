<?php
/*
Plugin Name: Grid Moja
Description: Grid Moja
Version: 0.5
Author: Nickel Pro
Author URI: http://dukapress.org/
Plugin URI: http://dukapress.org/
*/

/*This file handles global settings and definitions*/

session_start();
define('GM_PLUGIN_URL', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
define('GM_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)));

/*DEPENDENCIES*/
require_once('php/image.php');
require_once('php/grid.php');

/*SETTINGS*/
define ('DEFAULT_IMAGE_ON', false);	//use branded default image
define ('POST_CONTENT_ON', false);	//use content instead of excerpt

/** Includes CSS and JS **/
add_action('init', 'GM_register_style_js');
function GM_register_style_js () {
    if (!is_admin()) {        
        wp_register_style('GM_basic_css', GM_PLUGIN_URL.'/css/gm-basic.css');
		
		wp_enqueue_style( 'GM_basic_css' );		
		}
	}

/*Add Thumbnail Support*/
if (function_exists('add_theme_support')) {
  add_theme_support('post-thumbnails');
  add_image_size('gm-thumb',310,218,True);
}
?>