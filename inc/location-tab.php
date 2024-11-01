<?php
global $post;

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
?>

<?php if ( ! empty( $settings['map_quest_api_key'] ) ) : ?>
	<div class="map-wrap">
		<label for="wp-travel-mq-lat">
			<input type="text" name="wp_travel_lat" id="wp-travel-mq-lat" placeholder="latitude" value="<?php echo esc_html( $map_data['lat'] ); ?>" >
		</label>
		<label for="wp-travel-mq-lng">
			<input type="text" name="wp_travel_lng" id="wp-travel-mq-lng" placeholder="longitude" value="<?php echo esc_html( $map_data['lng'] ); ?>" >
		</label>
		<label for="mq-search-input">
			<input type="search" id="mq-search-input" placeholder="<?php esc_attr_e( 'Start Searching...', 'wp-travel-mapquest' ); ?>" />
		</label>
		<div id="mapQuest"></div>
	</div>
<?php else : ?>
	<div class="map-wrap">
		<p><?php echo sprintf( "Please add 'Map Quest api key' in the <a href=\"edit.php?post_type=%s&page=settings\">settings</a>", WP_TRAVEL_POST_TYPE ); ?></p>
	</div>
<?php endif; ?>
