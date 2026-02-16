<?php
/**
 * Core plugin class for Throughput Check.
 *
 * @package Throughput_Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpsani.store
 * @since      1.0.0
 *
 * @package    Throughput_Check
 * @subpackage Throughput_Check/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Throughput_Check
 * @subpackage Throughput_Check/includes
 * @author     sani060913 <support@wpsani.store>
 */
class Throughput_Check {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Throughput_Check_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The class responsible for defining REST API endpoints.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Throughput_Check_Rest    $rest    Defines REST API endpoints for the plugin.
	 */
	protected $rest;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'THROUGHPUT_CHECK_VERSION' ) ) {
			$this->version = THROUGHPUT_CHECK_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'throughput-check';
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_rest_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Throughput_Check_Loader. Orchestrates the hooks of the plugin.
	 * - Throughput_Check_Admin. Defines all hooks for the admin area.
	 * - Throughput_Check_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-throughput-check-loader.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-throughput-check-admin.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-throughput-check-public.php';
		/**
		 * The class responsible for defining REST API endpoints.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-throughput-check-rest.php';
		$this->loader = new Throughput_Check_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Throughput_Check_Admin( $this->get_plugin_name(), $this->get_version(), $this->loader );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Throughput_Check_Public( $this->get_plugin_name(), $this->get_version(), $this->loader );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the REST API endpoints
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_rest_hooks() {
		$rest = new Throughput_Check_Rest( $this->get_plugin_name(), $this->get_version(), $this->loader );
		$this->loader->add_action( 'rest_api_init', $rest, 'register_routes' );
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Throughput_Check_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
