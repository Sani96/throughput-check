<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Throughput_Check
 * @subpackage Throughput_Check/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Throughput_Check
 * @subpackage Throughput_Check/admin
 */
class Throughput_Check_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @param    object|null $loader Loader instance.
	 * @var      Throughput_Check_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of the plugin.
	 * @param    string $version           The version of the plugin.
	 * @param    object $loader            The loader instance.
	 */
	public function __construct( $plugin_name, $version, $loader = null ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->loader      = $loader;
		$this->setup_admin_hooks();
	}

	/**
	 * Setup all admin hooks cleanly.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function setup_admin_hooks() {
		$this->loader->add_action( 'admin_menu', $this, 'throughput_check_add_admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $this, 'register_license_settings' );
		$this->loader->add_action( 'admin_init', $this, 'handle_license_actions' );
		$this->loader->add_action( 'wp_ajax_throughput_check_run_test', $this, 'throughput_check_ajax_run_test' );
	}

	/**
	 * Add the admin menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function throughput_check_add_admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Throughput Check', 'throughput-check' ),
			__( 'Throughput Check', 'throughput-check' ),
			'manage_options',
			'throughput-check',
			array( $this, 'throughput_check_display_admin_page' )
		);
	}
	/**
	 * Display the admin page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function throughput_check_display_admin_page() {
		$snapshot = $this->get_environment_snapshot();
		require plugin_dir_path( __DIR__ ) . 'admin/partials/throughput-check-admin-display.php';
	}
	/**
	 * Enqueue styles for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string $hook_suffix The current admin page hook suffix.
	 * @return   void
	 */
	public function enqueue_styles( $hook_suffix ) {
		if ( 'tools_page_throughput-check' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/throughput-check-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Enqueue scripts for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string $hook_suffix The current admin page hook suffix.
	 * @return   void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( 'tools_page_throughput-check' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/throughput-check-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'throughput_check_admin',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'reset_nonce' => wp_create_nonce( 'throughput_check_reset_nonce' ),
				'view_nonce'  => wp_create_nonce( 'throughput_check_view_nonce' ),
				'run_nonce'   => wp_create_nonce( 'throughput_check_run_test' ),
			)
		);
	}

	/**
	 * Display the license page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function throughput_check_display_license_page() {
		require_once plugin_dir_path( __DIR__ ) . 'admin/partials/throughput-check-license-display.php';
	}

	/**
	 * Register license settings.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function register_license_settings() {

		register_setting(
			'throughput_check_license',
			'throughput_check_license_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'throughput_check_license',
			'throughput_check_license_status',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
				'default'           => 'missing',
			)
		);

		register_setting(
			'throughput_check_license',
			'throughput_check_license_checked_at',
			array(
				'type'    => 'integer',
				'default' => 0,
			)
		);
	}


	/**
	 * Handle license actions: activate, deactivate, check.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function handle_license_actions() {
		if ( ! is_admin() ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$nonce = isset( $_POST['throughput_check_license_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['throughput_check_license_nonce'] ) ) : '';
		if ( empty( $nonce ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, 'throughput_check_license_action' ) ) {
			return;
		}

		$do_activate   = isset( $_POST['throughput_check_license_activate'] );
		$do_deactivate = isset( $_POST['throughput_check_license_deactivate'] );
		$do_check      = isset( $_POST['throughput_check_license_check'] );

		if ( ! $do_activate && ! $do_deactivate && ! $do_check ) {
			return;
		}

		$license_key = trim( (string) get_option( 'throughput_check_license_key', '' ) );
		if ( '' === $license_key ) {
			update_option( 'throughput_check_license_status', 'missing' );
			$this->license_admin_redirect_with_notice( 'missing' );
			return;
		}

		if ( $do_activate ) {
			$result = $this->edd_license_request( 'activate_license', $license_key );
			$status = $this->normalize_edd_status( $result );
			update_option( 'throughput_check_license_status', $status );
			update_option( 'throughput_check_license_checked_at', time() );
			$this->license_admin_redirect_with_notice( $status );
			return;
		}

		if ( $do_deactivate ) {
			$result = $this->edd_license_request( 'deactivate_license', $license_key );
			$status = $this->normalize_edd_status( $result, true );
			update_option( 'throughput_check_license_status', $status );
			update_option( 'throughput_check_license_checked_at', time() );
			$this->license_admin_redirect_with_notice( $status );
			return;
		}

		if ( $do_check ) {
			$result = $this->edd_license_request( 'check_license', $license_key );
			$status = $this->normalize_edd_status( $result );
			update_option( 'throughput_check_license_status', $status );
			update_option( 'throughput_check_license_checked_at', time() );
			$this->license_admin_redirect_with_notice( $status );
			return;
		}
	}

	/**
	 * Make a request to the EDD license server.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $action       The action to perform: activate_license, deactivate_license, check_license.
	 * @param    string $license_key  The license key.
	 * @return   array                 The response data.
	 */
	private function edd_license_request( $action, $license_key ) {
		$store_url = 'https://wpsani.store';
		$item_id   = 1503;

		$response = wp_remote_post(
			$store_url,
			array(
				'timeout' => 15,
				'body'    => array(
					'edd_action' => $action,
					'license'    => $license_key,
					'item_id'    => $item_id,
					'url'        => home_url(),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'ok'    => false,
				'error' => $response->get_error_message(),
			);
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );
		if ( 200 !== $code || ! is_array( $data ) ) {
			return array(
				'ok'    => false,
				'error' => 'invalid_response',
				'raw'   => $body,
				'code'  => $code,
			);
		}

		return $data;
	}

	/**
	 * Normalize the EDD license status.
	 *
	 * @since    1.0.1
	 * @access   private
	 * @param    array $data            The response data from EDD.
	 * @param    bool  $is_deactivate   Whether the action was deactivate_license.
	 * @return   string                   The normalized license status.
	 */
	private function normalize_edd_status( $data, $is_deactivate = false ) {
		if ( ! is_array( $data ) ) {
			return 'invalid';
		}

		if ( isset( $data['license'] ) && is_string( $data['license'] ) ) {
			$license = sanitize_key( $data['license'] );

			if ( $is_deactivate && 'deactivated' === $license ) {
				return 'inactive';
			}
			return $license;
		}

		return 'invalid';
	}

	/**
	 * Redirect to the license admin page with a notice.
	 *
	 * @since    1.0.1
	 * @access   private
	 * @param    string $status    The license status to show in the notice.
	 * @return   void
	 */
	private function license_admin_redirect_with_notice( $status ) {
		$url = add_query_arg(
			array(
				'page'                            => 'throughput-check-license',
				'throughput_check_license_notice' => $status,
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Display the options page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function throughput_check_display_options_page() {
		require_once plugin_dir_path( __DIR__ ) . 'admin/partials/throughput-check-options-display.php';
	}

	/**
	 * Get a minimal environment snapshot for the admin page.
	 *
	 * @return array<string, string>
	 */
	public function get_environment_snapshot() {
		global $wpdb;

		$php_version = function_exists( 'phpversion' ) ? phpversion() : '';
		$mysql_ver   = method_exists( $wpdb, 'db_version' ) ? $wpdb->db_version() : '';

		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_active = (array) get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins = array_merge( $active_plugins, array_keys( $network_active ) );
		}

		$server_software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		return array(
			'PHP'            => $php_version ? $php_version : '—',
			'Memory limit'   => ini_get( 'memory_limit' ) ? (string) ini_get( 'memory_limit' ) : '—',
			'Max exec'       => ini_get( 'max_execution_time' ) ? (string) ini_get( 'max_execution_time' ) . 's' : '—',
			'WP_DEBUG'       => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'On' : 'Off',
			'Object cache'   => wp_using_ext_object_cache() ? 'Yes' : 'No',
			'MySQL'          => $mysql_ver ? $mysql_ver : '—',
			'Server'         => $server_software ? $server_software : '—',
			'Active plugins' => (string) count( array_unique( $active_plugins ) ),
		);
	}

	/**
	 * Handle the AJAX request to run the throughput test.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   void
	 */
	public function throughput_check_ajax_run_test() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
		}

		check_ajax_referer( 'throughput_check_run_test', 'nonce' );

		$timeout = 3;

		$stage = isset( $_POST['stage'] ) ? sanitize_key( wp_unslash( $_POST['stage'] ) ) : 'medium';

		$stage_map = array(
			'small'  => 5,
			'medium' => 15,
			'large'  => 30,
		);

		$concurrency = isset( $stage_map[ $stage ] ) ? $stage_map[ $stage ] : 15;

		$path = '/wp-json/throughput-check/v1/profile?mode=real';
		$home = $this->build_loopback_url( $path );

		// If the home URL contains a port (e.g., localhost:8080), replace the host with 127.0.0.1.
		if ( preg_match( '#:\d+/#', $home ) ) {
			$url = 'http://127.0.0.1' . $path;
		} else {
			$url = $home;
		}
		$start = microtime( true );

		// Prepare 15 requests with blocking=false.
		if ( ! class_exists( 'Requests' ) ) {
			require_once ABSPATH . WPINC . '/class-requests.php';
		}

		$requests = array();
		for ( $i = 0; $i < $concurrency; $i++ ) {
			$requests[ 'r' . $i ] = array(
				'url'     => $url,
				'headers' => array( 'Cache-Control' => 'no-cache' ),
				'options' => array(
					'timeout'          => $timeout,
					'verify'           => false,
					'follow_redirects' => false,
				),
			);
		}

		$t0        = microtime( true );
		$responses = Requests::request_multiple( $requests );
		$server_ms = array();
		$errors    = 0;
		$t1        = microtime( true );

		foreach ( $responses as $r ) {
			if ( $r instanceof Requests_Exception ) {
				++$errors;
				continue;
			}

			$code = (int) $r->status_code;
			if ( $code < 200 || $code >= 300 ) {
				++$errors;
				continue;
			}

			$body = (string) $r->body;
			$data = json_decode( $body, true );

			if ( is_array( $data ) && isset( $data['elapsed_ms'] ) ) {
				$server_ms[] = (float) $data['elapsed_ms'];
			}
		}

		sort( $server_ms );

		$server_p50 = 0.0;
		$server_p95 = 0.0;

		$n = count( $server_ms );
		if ( $n > 0 ) {
			$server_p50 = $server_ms[ (int) floor( 0.50 * ( $n - 1 ) ) ];
			$server_p95 = $server_ms[ (int) floor( 0.95 * ( $n - 1 ) ) ];
		}

		$batch_sec     = max( 0.0001, ( $t1 - $t0 ) );
		$client_avg_ms = round( ( $batch_sec / $concurrency ) * 1000, 2 );
		$estimated_rps = round( $concurrency / $batch_sec, 2 );

		$stability = 'Slow';
		if ( $errors > 0 ) {
			$stability = 'Unstable';
		} elseif ( $client_avg_ms < 200 ) {
			$stability = 'Good';
		} elseif ( $client_avg_ms <= 500 ) {
			$stability = 'Moderate';
		}

		$grade = 'D';
		if ( $estimated_rps >= 100 ) {
			$grade = 'A';
		} elseif ( $estimated_rps >= 50 ) {
			$grade = 'B';
		} elseif ( $estimated_rps >= 20 ) {
			$grade = 'C';
		}

		wp_send_json_success(
			array(
				'stage'          => $stage,
				'concurrency'    => $concurrency,
				'client_avg_ms'  => $client_avg_ms,
				'batch_sec'      => round( $batch_sec, 2 ),
				'estimated_rps'  => $estimated_rps,
				'errors'         => $errors,
				'stability'      => $stability,
				'url_used'       => $url,
				'server_p50_ms'  => round( $server_p50, 2 ),
				'server_p95_ms'  => round( $server_p95, 2 ),
				'server_samples' => $n,
				'grade'          => $grade,
			)
		);
	}

	/**
	 * Build the internal loopback URL used for the test.
	 *
	 * @param string $path Path starting with /wp-json/...
	 * @return string
	 */
	private function build_loopback_url( $path ) {
		$path = '/' . ltrim( $path, '/' );

		// 1) Optional override (best for Docker / unusual setups).
		if ( defined( 'THROUGHPUT_CHECK_LOOPBACK_BASE' ) && is_string( THROUGHPUT_CHECK_LOOPBACK_BASE ) && THROUGHPUT_CHECK_LOOPBACK_BASE ) {
			return rtrim( THROUGHPUT_CHECK_LOOPBACK_BASE, '/' ) . $path;
		}

		$home = home_url( '/' );
		$u    = wp_parse_url( $home );

		$scheme = isset( $u['scheme'] ) ? $u['scheme'] : 'http';
		$host   = isset( $u['host'] ) ? $u['host'] : 'localhost';

		// If site is running on localhost with a port (common in local dev),
		// inside the container the correct loopback is usually 127.0.0.1:80 (no port).
		// Keep it simple: if a port is present, drop it and use 127.0.0.1.
		if ( isset( $u['port'] ) && (int) $u['port'] > 0 ) {
			$scheme = 'http';
			$host   = '127.0.0.1';
		}

		return $scheme . '://' . $host . $path;
	}
}
