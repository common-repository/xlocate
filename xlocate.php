<?php
/*
Plugin Name: xLocate
Description: X marks the spot. xLocate is a plugin that allows you to create and manage listings with locations on your WordPress website. xLocate uses the google API to easily and intuitively add, listing and add them to a Map.
Plugin URI: https://wordpress.org/plugins/xlocate/
Author: codemanas
Author URI: https://codemanas.com
Version: 1.0.1
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
Text Domain: xlocate
Domain Path: /language
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'XLOC_VERSION', '1.0.0' );
define( 'XLOC_PREFIX_SLUG', 'xloc' );
define( 'XLOC_DIR_PATH', dirname( __FILE__ ) );
define( 'XLOC_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'XLOC_INCLUDES_PATH', XLOC_DIR_PATH . '/inc/' );
define( 'XLOC_TEMPLATES_PATH', XLOC_DIR_PATH . 'templates' );
define( 'XLOC_BASENAME', plugin_basename( __FILE__ ) );

/*** Load Plugin Admin Option */
require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-admin.php' );

/*** Load Custom Post Type for Plugin */
require_once( XLOC_DIR_PATH . '/includes/custom-post-type.php' );

/*** Load helper funcitons for plugin */
require_once( XLOC_DIR_PATH . '/includes/helper-functions.php' );

/*** Load The Meta Box, Generates Map and Other Required Meta Box Fields */
require_once( XLOC_DIR_PATH . '/admin/classes/class-meta-box.php' );

/*** Load the xLocate Handler, handles all the logic for updating custom table */
require_once( XLOC_DIR_PATH . '/admin/classes/class-xlocate-location.php' );

/*** Load Shortcodes */
require_once( XLOC_DIR_PATH . '/includes/shortcodes.php' );
// the main plugin class
require_once dirname( __FILE__ ) . '/includes/xlocate-init.php';

/*Handles All Search Logic*/
require_once( XLOC_DIR_PATH . '/frontend/classes/class-xlocate-search-handler.php' );

add_action( 'plugins_loaded', array( 'xlocate_Init', 'instance' ) );
register_activation_hook( __FILE__, array( 'xlocate_Init', 'xloc_activate_plugin' ) );
register_deactivation_hook( __FILE__, array( 'xlocate_Init', 'xloc_uninstall_plugin' ) );