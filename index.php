<?php
/**
 * Plugin Name: Fusion Builder Views Addon
 * Description: Now you can display the views in fusion builder!
 * Version: 1.0
 * Author: Bradatan Dorin
 *
 * @package Fusion_Views_Addon
 */

use Fusion_Views_Addon\Views_Counter;

// Plugin Folder Path.
if ( ! defined( 'FUSION_VIEWS_ADDON_PLUGIN_DIR' ) ) {
	define( 'FUSION_VIEWS_ADDON_PLUGIN_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}

// Plugin Folder URL.
if ( ! defined( 'FUSION_VIEWS_ADDON_PLUGIN_URL' ) ) {
	define( 'FUSION_VIEWS_ADDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! class_exists( 'Fusion_Views_Addon' ) ) {

	/**
	 * Initialize the fusion views element.
	 */
	function my_init_elements() {
		include_once wp_normalize_path( FUSION_VIEWS_ADDON_PLUGIN_DIR . '/elements/views-counter.php' );
	}
	add_action( 'fusion_builder_shortcodes_init', 'my_init_elements', 10 );

}

if ( ! class_exists( Views_Counter::class ) ) {
	include_once wp_normalize_path( FUSION_VIEWS_ADDON_PLUGIN_DIR . '/inc/class-views-counter.php' );
	include_once wp_normalize_path( FUSION_VIEWS_ADDON_PLUGIN_DIR . '/inc/plugin-functions.php' );

	add_action( 'wp', array( Views_Counter::class, 'increase_post_views_on_page_load' ) );
}
