<?php
/**
 * Plugin Name: WP Travel MapQuest
 * Plugin URI: http://www.wensolutions.com/plugins/ws-theme-addons
 * Description: Provides Additional Features and functionalities for WP Travel.
 * Version:           3.0.0
 * Author: WP Travel
 * Author URI: https://wptravel.io
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.2.2
 *
 * Text Domain: wp-travel-mapquest
 * Domain Path: /i18n/languages/
 *
 * @package WP Travel MapQuest
 * @author WenSolutions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define WPTMQ_FILE.
if ( ! defined( 'WPTMQ_FILE' ) ) {
	define( 'WPTMQ_FILE', __FILE__ );
}

// Include main class.
if ( ! class_exists( 'WP_Travel_MapQuest' ) ) {
	include_once dirname( __FILE__ ) . '/inc/class-wp-travel-mapquest.php';
}

add_filter( 'wp_travel_maps', 'wp_travel_mapquest_add_option' );
/**
 * Adds here map option to the WP Travel Map Selector.
 *
 * @param array $wp_travel_maps WP Travel Map array.
 * @return array $wp_travel_maps Here Map added array.
 * @since 1.0.1
 */
function wp_travel_mapquest_add_option( $wp_travel_maps ) {
	$wp_travel_maps['wp-travel-mapquest'] = __( 'MapQuest', 'wp-travel-mapquest' );
	return $wp_travel_maps;
}

/**
 * Main instance of WP Travel MapQuest.
 *
 * Returns the main instance of WP_Travel_MapQuest to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return wp_travel_mapquest
 */
function wp_travel_mapquest() {
	return WP_Travel_MapQuest::instance();
}

add_action( 'plugins_loaded', 'wp_travel_mapquest' );
