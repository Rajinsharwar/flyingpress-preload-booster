<?php
/*
 * Plugin Name:       FlyingPress Preload Booster
 * Plugin URI:        https://linkedin.com/in/rajinsharwar
 * Description:       Boosts the preload speed of the URLs.
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rajin Sharwar
 * Author URI:        https://linkedin.com/in/rajinsharwar
 * Text Domain:       flyingpress-preload-boost
 */
defined( 'ABSPATH' ) || exit;

function flyingpress_preload_booster_has_pending_preload() {
	global $wpdb;

	$table = $wpdb->prefix . 'actionscheduler_actions';

	$hook   = 'flying_press_preload_url';
	$pending = $wpdb->esc_like( 'pending' );
	$running = $wpdb->esc_like( 'in-progress' );

	$sql = $wpdb->prepare(
		"SELECT COUNT(action_id) 
		 FROM {$table}
		 WHERE hook = %s
		   AND status IN (%s, %s)",
		$hook,
		$pending,
		$running
	);

	$count = (int) $wpdb->get_var( $sql );

	return $count > 0;
}

if ( true !== flyingpress_preload_booster_has_pending_preload() ) {
	add_action(
		'admin_bar_menu',
		function( $wp_admin_bar ) {
			if ( ! is_admin() ) {
					return;
			}

			$id    = 'preload_booster_indicator';
			$label = 'Preload Booster OFF — Click to Refresh';
			$title  = '<span class="fp-preload-booster-indicator">' . esc_html( $label ) . '</span>';

			// Simple page refresh by linking to the current URL.
			$href = esc_url( add_query_arg( array() ) );

			$wp_admin_bar->add_node(
					array(
							'id'    => $id,
							'title' => $title,
							'href'  => $href,
							'meta'  => array(
									'title' => esc_attr__( 'Preload Booster is currently inactive. Click to refresh.', 'flyingpress-preload-booster' ),
							),
					)
			);
		},
		999
	);

	add_action(
		'admin_head',
		function() {
				?>
				<style>
					.fp-preload-booster-indicator {
							background: blue;
							color: #ffffff;
							padding: 0 8px 0 7px !important;
							border: 1px solid #ffffff;
							border-radius: 3px;
							font-size: 12px;
							font-weight: 600;
							line-height: 1.4;
							display: inline-block;
					}
				</style>
				<?php
		}
	);
	
	return;
}
/**
 * after checking if urls in queue
 */
require_once __DIR__ . '/inc/init.php';

add_filter( 'heartbeat_received', 'flyingpress_preload_booster_run_as_on_heartbeat', 10, 2 );

function flyingpress_preload_booster_run_as_on_heartbeat( $response, $data ) {
	if ( ! function_exists( 'spawn_cron' ) ) {
		require_once ABSPATH . 'wp-includes/cron.php';
	}

	$event = 'action_scheduler_run_queue';

	$next = wp_next_scheduled( $event );

	if ( false !== $next ) {
		spawn_cron( $next );
	}

	return $response;
}