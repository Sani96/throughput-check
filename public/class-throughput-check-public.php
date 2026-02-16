<?php
/**
 * Public-facing functionality for League Standings Widget.
 *
 * @package Throughput_Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Throughput_Check
 * @subpackage Throughput_Check/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Throughput_Check
 * @subpackage Throughput_Check/public
 * @author     sani060913 <support@wpsani.store>
 */
class Throughput_Check_Public {
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
	 * @param      string      $plugin_name       The name of the plugin.
	 * @param      string      $version    The version of this plugin.
	 * @param    object|null $loader Loader instance.
	 * @return void
	 */
	public function __construct( $plugin_name, $version, $loader = null ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->loader      = $loader;
		$this->setup_hooks();
	}

	/**
	 * Register the hooks for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function setup_hooks() {
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Throughput_Check_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The League_Standings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( is_admin() || wp_doing_ajax() || is_feed() ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/throughput-check-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Throughput_Check_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Throughput_Check_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/throughput-check-public.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'throughputCheckData',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'throughput_check_nonce' ),
				'post_id'  => get_queried_object_id(),
			)
		);
	}
}
