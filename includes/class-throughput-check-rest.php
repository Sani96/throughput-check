<?php
/**
 * REST API Endpoints
 *
 * @package Throughput_Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Define REST API endpoints.
 *
 * @since      1.0.0
 * @package    Throughput_Check
 * @subpackage Throughput_Check/includes
 * @author     sani060913 <wp@wpsani.store>
 */
class Throughput_Check_Rest {

	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'throughput-check/v1',
			'/ping',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'ping' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'throughput-check/v1',
			'/profile',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'profile' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'mode' => array(
						'default'           => 'light',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);
	}

	/**
	 * Ping endpoint
	 *
	 * @return WP_REST_Response
	 */
	public function ping() {
		global $wpdb;

		$start = microtime( true );

		// DB call with wp_cache support (if enabled).
		$now = wp_cache_get( 'tc_ping_db_now', 'throughput_check' );
		if ( false === $now ) {
			$now = $wpdb->get_var( 'SELECT NOW()' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			wp_cache_set( 'tc_ping_db_now', $now, 'throughput_check', 30 );
		}
		$elapsed_ms = ( microtime( true ) - $start ) * 1000;

		return new WP_REST_Response(
			array(
				'ok'         => true,
				'ts'         => time(),
				'db_now'     => is_string( $now ) ? $now : '',
				'elapsed_ms' => round( $elapsed_ms, 2 ),
			),
			200
		);
	}

	/**
	 * Profile endpoint (light/real).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function profile( $request ) {
		$mode = $request->get_param( 'mode' );
		$mode = is_string( $mode ) ? sanitize_key( $mode ) : 'light';
		$mode = in_array( $mode, array( 'light', 'real' ), true ) ? $mode : 'light';

		$start = microtime( true );

		if ( 'real' === $mode ) {
			// 1) Get some options (simulate typical front-end load).
			get_option( 'blogname' );
			get_option( 'blogdescription' );
			get_option( 'permalink_structure' );

			// 2) Small WP_Query (5 public posts).
			$q = new WP_Query(
				array(
					'post_type'              => 'post',
					'post_status'            => 'publish',
					'posts_per_page'         => 5,
					'no_found_rows'          => true,
					'ignore_sticky_posts'    => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'fields'                 => 'ids',
				)
			);

			$post_ids = is_array( $q->posts ) ? $q->posts : array();

			// 3) Cache touch (if object cache is on, behavior differs).
			$cache_key = 'tc_profile_' . wp_rand( 1, 1000000 );
			wp_cache_set( $cache_key, $post_ids, 'throughput_check', 30 );
			wp_cache_get( $cache_key, 'throughput_check' );
		}

		$elapsed_ms = ( microtime( true ) - $start ) * 1000;

		return new WP_REST_Response(
			array(
				'ok'         => true,
				'mode'       => $mode,
				'elapsed_ms' => round( $elapsed_ms, 2 ),
			),
			200
		);
	}
}
