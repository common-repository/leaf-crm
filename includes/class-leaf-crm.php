<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.leaf.my
 * @since      1.0.0
 *
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
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
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
 * @author     Leaf CRM Team <developer@leaf.my>
 */
class Leaf_Crm {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Leaf_Crm_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $leaf_crm    The string used to uniquely identify this plugin.
	 */
	protected $leaf_crm;

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
		if ( defined( 'LEAF_CRM_VERSION' ) ) {
			$this->version = LEAF_CRM_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->leaf_crm = 'leaf-crm';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_integration_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Leaf_Crm_Loader. Orchestrates the hooks of the plugin.
	 * - Leaf_Crm_i18n. Defines internationalization functionality.
	 * - Leaf_Crm_Admin. Defines all hooks for the admin area.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leaf-crm-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leaf-crm-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leaf-crm-admin.php';

		/**
		 * The class responsible to managing the configuration and options for the plugin
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-leaf-crm-options.php';

		/**
		 * The class responsible to perform lead submission to Leaf CRM
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-leaf-crm-api.php';

		/**
		 * WP 3rd Party Contact Forms plugins integrations
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/contact-forms/class-leaf-crm-cf7.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/contact-forms/class-leaf-crm-wpforms.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/contact-forms/class-leaf-crm-forminator-form.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/contact-forms/class-leaf-crm-ninja-form.php';

		$this->loader = new Leaf_Crm_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Leaf_Crm_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Leaf_Crm_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Leaf_Crm_Admin( $this->get_leaf_crm(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'leaf_crm_plugin_menu', 9);

		$plugin_options = new Leaf_Crm_Options();
		$hook_name = 'wp_ajax_' . Leaf_Crm_Constants::WP_SAVE_HOOK_NAME;
		$this->loader->add_action($hook_name, $plugin_options, 'save_options_handler');
	}

	private function define_integration_hooks() {
		/**
		 * WP Contact Form 7
		 * Reference: https://contactform7.com/2020/07/28/accessing-user-input-data/
		 */
		$leaf_cf7 = new Leaf_Crm_CF7();
		$this->loader->add_action('wpcf7_before_send_mail', $leaf_cf7, 'submit_cf7_to_leaf', 10);

		/**
		 * WPForms Integration
		 * Reference: https://wpforms.com/developers/wpforms_process_complete/
		 */
		$leaf_wpforms = new Leaf_Crm_Wpforms();
		$this->loader->add_action('wpforms_process_complete', $leaf_wpforms, 'submit_wpforms_to_leaf', 10, 4);

		/**
		 * Forminator Integration
		 * References: 
		 *      https://wordpress.org/support/topic/send-form-submission-to-webhook/
		 *      https://wordpress.org/support/topic/custom-code-forminator_form_after_save_entry-called-if-nothing-was-saved/
		 */
		$leaf_forminator = new Leaf_Crm_Forminator_Form();
		$this->loader->add_action('forminator_form_after_save_entry', $leaf_forminator, 'submit_forminator_to_leaf', 10, 2);

		/**
		 * Ninja Form Integration
		 * Reference: https://developer.ninjaforms.com/codex/submission-processing-hooks/
		 */
		$leaf_ninja_form = new Leaf_Crm_Ninja_Form();
		$this->loader->add_action('ninja_forms_after_submission', $leaf_ninja_form, 'submit_ninja_form_to_leaf');
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
	public function get_leaf_crm() {
		return $this->leaf_crm;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Leaf_Crm_Loader    Orchestrates the hooks of the plugin.
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
