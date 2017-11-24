<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.multidots.com/
 * @since             1.0.0
 * @package           Library_Book_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Library Book Search Plugin
 * Plugin URI:        #
 * Description:       Library Book Search Plugin helps you to add,update,delete,listing&search books.
					  Seaching & Listing of books is based on AJAX.
					  You can easily add listing in page or post with help of shortcode. 
 * Version:           1
 * Author:            Megha Shah
 * Author URI:        #
 * Text Domain:       library-book-search
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'LBS_PLUGIN_NAME_VERSION', '1.0.0' );
define( 'LBS_PLUGIN_FILE_PATH', __FILE__ );
define( 'LBS_PLUGIN_CLASS_PATH', plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search.php' );
define( 'LBS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'LBS_PLUGIN_BASE_PATH', basename( __FILE__ ) );

// Required files for registering the post type and taxonomies and custom fields at activation time. Also add static 25 records for testing purpose.
require ( LBS_PLUGIN_CLASS_PATH );

Library_Book_Search::get_instance();