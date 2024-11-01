<?php
/**
 * WP Travel Mapquest
 *
 * @package WP Travel Mapquest
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main Class.
 */
class WP_Travel_MapQuest {

	const WP_TRAVEL_MAPQUEST_HANDLE = 'wp_travel_mapquest_';

	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	public $version = '2.1.6';

	/**
	 * The single instance of the class.
	 *
	 * @var $_instancce
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The Plugin name.
	 *
	 * @var $plugin_name
	 */
	public $plugin_name = 'wp-travel-mapquest';

	/**
	 * WP_Travel_MapQuest Constructor.
	 */
	private function __construct() {
		add_action( 'admin_init', array( $this, 'wp_travel_check_dependency' ) );

		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Main WP_Travel_MapQuest Instance.
	 * Ensures only one instance of WP_Travel_MapQuest is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_Travel_MapQuest()
	 * @return WP_Travel_MapQuest - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Defines Plugin constants.
	 */
	public function define_constants() {
		define( 'WPTMQ_ABSPATH', plugin_dir_path( WPTMQ_FILE ) );
		define( 'WPTMQ_URL', plugin_dir_url( WPTMQ_FILE ) );
		define( 'WPTMQ_VERSION', $this->version );
	}

	/**
	 * Includes Essential Plugin Files.
	 */
	public function includes() {
		include_once WPTMQ_ABSPATH . 'inc/helpers.php';
	}

	/**
	 * Init Hooks.
	 */
	public function init_hooks() {
		if ( ! defined( 'WP_TRAVEL_POST_TYPE' ) ) {
			return;
		}
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}
		// Hook to add mapquest settings fields.
		add_action( 'wp_travel_settings_after_currency', array( $this, 'add_settings' ), 10, 2 );
		// Add Mapquest tab hook.
		add_action( 'wp_travel_admin_map_area', array( $this, 'map_quest_backend_tab_cb' ), 10, 2 );
		// Add Mapquest Frontend Hook.
		// add_action( 'wp_travel_single_trip_after_header', array( $this, 'map_quest_frontend_tab_cb' ), 21 );
		add_action( 'wptravel_trip_map_wp-travel-mapquest', array( $this, 'map_quest_frontend_tab_cb' ), 21 );
		// Hook to save mapquest Settings.
		add_filter( 'wp_travel_before_save_settings', array( $this, 'push_settings' ) );
		add_filter( 'wp_travel_settings_fields', array( $this, 'settings_fields_v4' ) );
		add_filter( 'wp_travel_settings_values', array( $this, 'settings_fields_v4_values' ) );

		// Admin localize hook.
		add_filter( 'wp_travel_localize_gallery_data', array( $this, 'localize_admin_map_data' ) );
		// Public Localize Hook.
		add_filter( 'wptravel_localized_data', array( $this, 'localize_public_map_data' ) );
		add_filter( 'wp_travel_save_trip_metas', array( __CLASS__, 'add_meta_key_values' ) ); // Updates trip meta array.
	}

	/**
	 * Appends additonal mapquests settings to the Default WP Travel Settings.
	 *
	 * @param array $settings WP Travel Settings array.
	 */
	public function push_settings( $settings ) {

		$settings['map_quest_api_key']    = ! empty( $_POST['map_quest_api_key'] ) ? $_POST['map_quest_api_key'] : '';
		$settings['map_quest_zoom_level'] = ! empty( $_POST['map_quest_zoom_level'] ) ? $_POST['map_quest_zoom_level'] : '';

		return $settings;
	}

	public function settings_fields_v4 ( $settings ) {
		$settings['map_quest_api_key']    = ! empty( $settings['map_quest_api_key'] ) ? $settings['map_quest_api_key'] : '';
		$settings['map_quest_zoom_level'] = ! empty( $settings['map_quest_zoom_level'] ) ? $settings['map_quest_zoom_level'] : 15;

		return $settings;
	}

	public function settings_fields_v4_values ( $settings ) {
		if ( function_exists( 'wptravel_settings_default_fields' ) ) {
			$default_settings = wptravel_settings_default_fields();
		} else {
			$default_settings = wp_travel_settings_default_fields();
		}
		$settings['map_quest_api_key']    = ! empty( $settings['map_quest_api_key'] ) ? $settings['map_quest_api_key'] : $default_settings['map_quest_api_key'];
		$settings['map_quest_zoom_level'] = ! empty( $settings['map_quest_zoom_level'] ) ? $settings['map_quest_zoom_level'] : $default_settings['map_quest_zoom_level'];

		return $settings;
	}

	/**
	 * Add Settings to WP Travel Setting Page.
	 *
	 * @param string $tab Tab key.
	 * @param mixed  $args Arguments.
	 */
	public function add_settings( $tab, $args ) {

		$class = 'wp-travel-mapquest';

		$map_quest_api_key    = isset( $args['settings']['map_quest_api_key'] ) ? $args['settings']['map_quest_api_key'] : '';
		$map_quest_zoom_level = isset( $args['settings']['map_quest_zoom_level'] ) ? $args['settings']['map_quest_zoom_level'] : '15';
		?>
		<tr class="wp-travel-map-option <?php echo $class; ?>">
			<th>
				<label for="map_quest_api_key"><?php echo esc_html__( 'MapQuest API Key', 'wp-travel-mapquest' ); ?></label>
			</th>
			<td>
				<input type="text" value="<?php echo esc_attr( $map_quest_api_key ); ?>" name="map_quest_api_key" id="map_quest_api_key"/>
				<p class="description"><?php echo sprintf( 'Don\'t have api key <a href="https://developer.mapquest.com/" target="_blank">click here</a>', 'wp-travel' ); ?></p>
			</td>
		</tr>
		<tr class="wp-travel-map-option <?php echo $class; ?>">
			<th>
				<label for="map_quest_zoom_level"><?php echo esc_html__( 'Map Zoom Level', 'wp-travel-mapquest' ); ?></label>
			</th>
			<td>
				<input step="1" min="1" type="number" value="<?php echo esc_attr( $map_quest_zoom_level ); ?>" name="map_quest_zoom_level" id="map_quest_zoom_level"/>
			</td>
		</tr>
		<?php
	}

	/**
	 * Updates $trip_meta array.
	 *
	 * @param array $trip_meta Trip Meta.
	 * @return array
	 */
	public static function add_meta_key_values( $trip_meta ) {
		$lat = isset( $_POST['wp_travel_lat'] ) ? $_POST['wp_travel_lat'] : '';
		$lng = isset( $_POST['wp_travel_lng'] ) ? $_POST['wp_travel_lng'] : '';

		$trip_meta['wp_travel_lat'] = $lat;
		$trip_meta['wp_travel_lng'] = $lng;
		return $trip_meta;
	}

	/**
	 * Callback for Backend Hook.
	 *
	 * @param string $tab Tab Key.
	 */
	public function map_quest_backend_tab_cb( $tab ) {

		if ( function_exists( 'wptravel_get_maps' ) ) {
			$wptravel_maps = wptravel_get_maps();
		} else {
			$wptravel_maps = wp_travel_get_maps();
		}

		if ( $wptravel_maps['selected'] !== 'wp-travel-mapquest' ) {
			return;
		}

		wp_enqueue_script( 'map-quest-admin-scripts' );

		include WPTMQ_ABSPATH . 'inc/location-tab.php';
	}

	/**
	 * This will uninstall this plugin if parent WP-Travel plugin not found
	 */
	public function wp_travel_check_dependency() {

		$plugin      = plugin_basename( WPTMQ_FILE );
		$plugin_data = get_plugin_data( WPTMQ_FILE, false );

		if ( ! class_exists( 'WP_Travel' ) ) {

			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( wp_kses_post( '<strong>' . $plugin_data['Name'] . '</strong> requires the WP Travel plugin to work. Please activate it first. <br /><br />Back to the WordPress <a href="' . esc_url( get_admin_url( null, 'plugins.php' ) ) . '">Plugins page</a>.' ) );
			}
		}
	}

	/**
	 * Callback for frontend Hook.
	 *
	 * @param int $trip_id Post ID.
	 */
	public function map_quest_frontend_tab_cb( $trip_id = 0, $data = array() ) {

		$data = function_exists( 'wptravel_get_maps' ) ? wptravel_get_maps() : wp_travel_get_maps();

		$current_map = $data['selected'];
		if ( 'wp-travel-mapquest' !== $current_map ) {
			return;
		}
		if ( function_exists( 'wptravel_get_map_data' ) ) {
			$map_data = wptravel_get_map_data();
		} else {
			$map_data = wp_travel_get_map_data();
		}

		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}

		if ( empty( $settings['map_quest_api_key'] ) ) {
			return;
		}
		wp_enqueue_script( 'map-quest-scripts' );
		$id = uniqid();
		$map_id = sprintf( 'wp-travel-map-%s', $id );
		?>
		<div class="wp-travel-map">
			<div id="<?php echo esc_attr( $map_id ); ?>" style="width:100%;height:300px"></div>
		</div>
		<script>
				jQuery(document).ready(function($) {
					// var options = {
					// 	lat : '27.693171845837',
					// 	lng : '85.281285846253',
					// }
					$( '#<?php echo esc_attr( $map_id ); ?>' ).wptravelMapquestMap();
				});
			</script>
		<?php
	}

	/**
	 * Enqueue Admin Scripts
	 *
	 * @return void
	 */
	public function admin_assets() {
		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}
		$wp_travel_react_switch = isset( $settings['wp_travel_switch_to_react'] ) && 'yes' === $settings['wp_travel_switch_to_react'];
		if ( $wp_travel_react_switch ) {
			$screen = get_current_screen();
			// settings_screen.
			if ( 'itinerary-booking_page_settings' == $screen->id ) {
				$deps                   = include_once sprintf( '%s/app/build/wp-travel-settings.asset.php', plugin_dir_path( WPTMQ_FILE ) );				
				$deps['dependencies'][] = 'jquery';
				wp_enqueue_script( self::WP_TRAVEL_MAPQUEST_HANDLE . 'admin-settings', WPTMQ_URL . '/app/build/wp-travel-settings.js', $deps['dependencies'], $deps['version'], true );
			}
		}
		$map_quest_api_key = isset( $settings['map_quest_api_key'] ) ? $settings['map_quest_api_key'] : '';
		if ( empty( $map_quest_api_key ) ) {
			return;
		}

		wp_register_script( 'map-quest-place-search-js', 'https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.js', array(), '1.0.0', true );
		wp_register_script( 'map-quest-js', 'https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js', array(), '1.3.2', true );
		if ( function_exists( 'wptravel_get_map_data' ) ) {
			$map_data = wptravel_get_map_data();
		} else {
			$map_data = wp_travel_get_map_data();
		}
		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}
		$mapquest_zoom_level       = $settings['map_quest_zoom_level'];
		$mapquest                  = array();
		$mapquest['latlng']['lat'] = ! empty( $map_data['lat'] ) ? $map_data['lat'] : '0';
		$mapquest['latlng']['lng'] = ! empty( $map_data['lng'] ) ? $map_data['lng'] : '0';
		$mapquest['apiKey']        = $map_quest_api_key;
		$mapquest['zoomLevel']     = $mapquest_zoom_level;
		wp_register_script( 'map-quest-admin-scripts', WPTMQ_URL . 'assets/admin-script.js', array( 'jquery', 'map-quest-place-search-js', 'map-quest-js' ), $this->version, true );

		// Map Styles.
		wp_enqueue_style( 'map-quest-place-search', 'https://api.mqcdn.com/sdk/place-search-js/v1.0.0/place-search.css', array(), '1.0.0', true );
		wp_enqueue_style( 'map-quest', 'https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css', array(), '1.3.2' );
		wp_enqueue_style( 'map-quest-backend-style', WPTMQ_URL . 'assets/css/backend-style.css', array(), '0.0.1' );

		/**
		 * @since 2.0.0
		 */
		$screen         = get_current_screen();
		$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries' );
		if ( in_array( $screen->id, $allowed_screen ) && isset( $settings['wp_travel_switch_to_react'] ) && 'yes' === $settings['wp_travel_switch_to_react'] ) {
			$deps = include_once sprintf( '%sapp/build/wp-travel-mapquest-admin-trip-options.asset.php', plugin_dir_path( WPTMQ_FILE ) );

			$deps['dependencies'] = array_merge( $deps['dependencies'], array( 'map-quest-place-search-js', 'map-quest-js' ) );
			wp_enqueue_script( 'wp-travel-mapquest-admin-trip-options', WPTMQ_URL . '/app/build/wp-travel-mapquest-admin-trip-options.js', $deps['dependencies'], $deps['version'], true );
			wp_enqueue_style( 'wp-travel-mapquest-admin-trip-options-style', WPTMQ_URL . '/app/build/wp-travel-mapquest-admin-trip-options.css', array( 'map-quest-place-search', 'map-quest' ), $deps['version'] );
		}
	}

	/**
	 * Enqueue Frontend Scripts.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}
		$map_quest_api_key = isset( $settings['map_quest_api_key'] ) ? $settings['map_quest_api_key'] : '';

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '.min';

		if ( empty( $map_quest_api_key ) ) {
			return;
		}

		if ( function_exists( 'wptravel_get_map_data' ) ) {
			$map_data = wptravel_get_map_data();
		} else {
			$map_data = wp_travel_get_map_data();
		}
		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}
		$mapquest_zoom_level       = $settings['map_quest_zoom_level'];
		$mapquest                  = array();
		$mapquest['latlng']['lat'] = $map_data['lat'];
		$mapquest['latlng']['lng'] = $map_data['lng'];
		$mapquest['apiKey']        = $map_quest_api_key;
		$mapquest['zoomLevel']     = $mapquest_zoom_level;

		// Map Quest Scripts.
		wp_register_script( 'map-quest-js', 'https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js', array(), '1.3.2', true );
		wp_register_script( 'map-quest-scripts', WPTMQ_URL . "assets/script{$suffix}.js", array( 'jquery', 'map-quest-js' ), $this->version, true );

		// Map Quest Styles.
		wp_enqueue_style( 'map-quest', 'https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css', array(), '1.3.2' );
	}

	/**
	 * Localize Map Date for here map.
	 *
	 * @param array $wp_travel_gallery_data Default data array.
	 * @return array $wpt_here_map Array contianing MapQuest data.
	 * @since 1.0.1
	 */
	public function localize_admin_map_data( $wp_travel_gallery_data ) {
		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}
		$map_quest_api_key = isset( $settings['map_quest_api_key'] ) ? $settings['map_quest_api_key'] : '';

		if ( function_exists( 'wptravel_get_map_data' ) ) {
			$map_data = wptravel_get_map_data();
		} else {
			$map_data = wp_travel_get_map_data();
		}
		$mapquest_zoom_level       = isset( $settings['map_quest_zoom_level'] ) ? $settings['map_quest_zoom_level'] : '';
		$mapquest                  = array();
		$mapquest['latlng']['lat'] = ! empty( $map_data['lat'] ) ? $map_data['lat'] : '0';
		$mapquest['latlng']['lng'] = ! empty( $map_data['lng'] ) ? $map_data['lng'] : '0';
		$mapquest['apiKey']        = $map_quest_api_key;
		$mapquest['zoomLevel']     = $mapquest_zoom_level;

		$wp_travel_gallery_data['mapquest'] = $mapquest;
		return $wp_travel_gallery_data;
	}

	/**
	 * Localize Map Date for here map.
	 *
	 * @param array $wp_travel Default data array.
	 * @return array $wp_travel Array contianing MapQuest data.
	 * @since 1.0.1
	 */
	public function localize_public_map_data( $localized ) {
		if ( function_exists( 'wptravel_get_settings' ) ) {
			$settings = wptravel_get_settings();
		} else {
			$settings = wp_travel_get_settings();
		}
		$map_quest_api_key = isset( $settings['map_quest_api_key'] ) ? $settings['map_quest_api_key'] : '';

		if ( empty( $map_quest_api_key ) ) {
			return $localized;
		}

		if ( function_exists( 'wptravel_get_map_data' ) ) {
			$map_data = wptravel_get_map_data();
		} else {
			$map_data = wp_travel_get_map_data();
		}
		
		$mapquest_zoom_level       = $settings['map_quest_zoom_level'];
		$mapquest                  = array();
		$mapquest['latlng']['lat'] = $map_data['lat'];
		$mapquest['latlng']['lng'] = $map_data['lng'];
		$mapquest['apiKey']        = $map_quest_api_key;
		$mapquest['zoomLevel']     = $mapquest_zoom_level;

		$localized['wp_travel']['mapquest']     = $mapquest; // Localized this into wp_travel key
		return $localized;
	}

}
