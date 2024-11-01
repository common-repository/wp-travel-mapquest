<?php
/**
 * Shorhand Function that removes the hooked action.
 *
 * @param string $action  Action Name.
 * @param string $class  Class Name.
 * @param string $method  Method Name.
 * @return void
 */
function wp_travel_mapquest_remove_class_action( $action, $class, $method ) {
	global $wp_filter;
	if ( isset( $wp_filter[ $action ] ) ) {
		$len = strlen( $method );
		foreach ( $wp_filter[ $action ] as $pri => $actions ) {
			foreach ( $actions as $name => $def ) {
				if ( substr( $name, -$len ) == $method ) {
					if ( is_array( $def['function'] ) ) {
						if ( get_class( $def['function'][0] ) == $class ) {
							if ( is_object( $wp_filter[ $action ] ) && isset( $wp_filter[ $action ]->callbacks ) ) {
								unset( $wp_filter[ $action ]->callbacks[ $pri ][ $name ] );
							} else {
								unset( $wp_filter[ $action ][ $pri ][ $name ] );
							}
						}
					}
				}
			}
		}
	}
}
