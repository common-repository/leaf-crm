<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.leaf.my
 * @since      1.0.0
 *
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/admin
 * @author     Leaf CRM Team <developer@leaf.my>
 */
class Leaf_Crm_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $leaf_crm    The ID of this plugin.
	 */
	private $leaf_crm;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $leaf_crm       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $leaf_crm, $version ) {

		$this->leaf_crm = $leaf_crm;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leaf_Crm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leaf_Crm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->leaf_crm, plugin_dir_url( __FILE__ ) . 'css/leaf-crm-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style($this->leaf_crm . "_toastr", plugin_dir_url(__FILE__) . 'css/toastr.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->leaf_crm . "_bootstrap", plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leaf_Crm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leaf_Crm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( $this->leaf_crm, plugin_dir_url( __FILE__ ) . 'js/leaf-crm-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script($this->leaf_crm . "_toastr", plugin_dir_url(__FILE__) . 'js/toastr.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->leaf_crm . "_bootstrap", plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', array('jquery'), $this->version, false);
		require_once plugin_dir_path(__FILE__) . 'partials/leaf-crm-admin-display.php';
	}

	public function leaf_crm_plugin_menu()
	{
		//The icon in Base64 format
		$icon_base64 = 'PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwXzk2Nl8yMDQwKSI+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMTEuMzg4OSAwQzUuMDk4OTggMCAwIDUuMDk4OTggMCAxMS4zODg5VjM4LjYxMTFDMCA0NC45MDEgNS4wOTg5OCA1MCAxMS4zODg5IDUwSDM4LjYxMTFDNDQuOTAxIDUwIDUwIDQ0LjkwMSA1MCAzOC42MTExVjExLjM4ODlDNTAgNS4wOTg5OCA0NC45MDEgMCAzOC42MTExIDBIMTEuMzg4OVpNOC45OTM0MyAxMi44MDE1QzkuNjI4MzIgMTIuNTk5MSAxMC4zMDcxIDEyLjk0OTcgMTAuNTA5NSAxMy41ODQ2QzExLjg0MDMgMTcuNzU4MyAxNC41NTUzIDIzLjQ2MjEgMTguMzE0NiAyNy4xMzAzQzIwLjE4MzIgMjguOTUzNyAyMi4yNDI2IDMwLjIwODkgMjQuNDU0NyAzMC41OTdDMjYuNjM2MyAzMC45Nzk3IDI5LjExMzIgMzAuNTQ1MyAzMS45MDMgMjguNzE5OUMzMi40NjA2IDI4LjM1NSAzMy4yMDg0IDI4LjUxMTMgMzMuNTczMyAyOS4wNjg5QzMzLjkzODEgMjkuNjI2NiAzMy43ODE5IDMwLjM3NDQgMzMuMjI0MiAzMC43MzkyQzI5Ljk5OTYgMzIuODQ5MiAyNi45MDkyIDMzLjQ3NzUgMjQuMDM3OCAzMi45NzM5QzIxLjE5NjkgMzIuNDc1NSAxOC43MTc3IDMwLjg5NTMgMTYuNjI5MyAyOC44NTc1QzEyLjQ3NDYgMjQuODAzNiA5LjYwMjIgMTguNjgyOCA4LjIxMDM5IDE0LjMxNzZDOC4wMDc5NiAxMy42ODI3IDguMzU4NTQgMTMuMDA0IDguOTkzNDMgMTIuODAxNVpNNDEuMDk4MiAzNy4xNjQ5QzQwLjQ2MzMgMzcuMzY3NCAzOS43ODQ2IDM3LjAxNjggMzkuNTgyMSAzNi4zODE5QzM4Ljg4NzcgMzQuMjAzOSAzNy44MTYzIDMxLjYwOTIgMzYuNDE2MiAyOS4xMDQ1QzM2LjMzMTMgMjkuMDU1NCAzNi4yNTEgMjguOTk1MiAzNi4xNzc1IDI4LjkyMzdDMzEuMTA0OSAyMy45ODgyIDI2LjEyNjQgMjUuMTQzMyAyNC40NTEgMjYuMTI4OEMyMy44NzY3IDI2LjQ2NjcgMjMuMTM3MSAyNi4yNzUgMjIuNzk5MyAyNS43MDA2QzIyLjQ2MTQgMjUuMTI2MiAyMi42NTMxIDI0LjM4NjcgMjMuMjI3NSAyNC4wNDg4QzI1LjAwODMgMjMuMDAxMyAyOC41OTUzIDIyLjEzNTUgMzIuNjI1MiAyMy43MTY0QzMyLjM0ODggMjMuNDEyIDMyLjA2NiAyMy4xMTggMzEuNzc3MSAyMi44MzYxQzI5LjkwODQgMjEuMDEyOCAyNy44NDkxIDE5Ljc1NzUgMjUuNjM2OSAxOS4zNjk1QzIzLjQ1NTMgMTguOTg2OCAyMC45Nzg0IDE5LjQyMTIgMTguMTg4NyAyMS4yNDY1QzE3LjYzMTEgMjEuNjExNCAxNi44ODMzIDIxLjQ1NTEgMTYuNTE4NCAyMC44OTc1QzE2LjE1MzUgMjAuMzM5OSAxNi4zMDk4IDE5LjU5MjEgMTYuODY3NCAxOS4yMjcyQzIwLjA5MjEgMTcuMTE3MyAyMy4xODI1IDE2LjQ4ODkgMjYuMDUzOSAxNi45OTI2QzI4Ljg5NDcgMTcuNDkwOSAzMS4zNzQgMTkuMDcxMiAzMy40NjI0IDIxLjEwODlDMzcuNjE3IDI1LjE2MjkgNDAuNDg5NSAzMS4yODM2IDQxLjg4MTMgMzUuNjQ4OEM0Mi4wODM3IDM2LjI4MzcgNDEuNzMzMSAzNi45NjI1IDQxLjA5ODIgMzcuMTY0OVoiIGZpbGw9IiNBN0FBQUQiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMF85NjZfMjA0MCI+CjxyZWN0IHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0id2hpdGUiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K';
		//The icon in the data URI scheme
		$icon_data_uri = 'data:image/svg+xml;base64,' . $icon_base64;

		add_menu_page("Leaf CRM", "Leaf CRM", 'manage_options', "leaf-crm", "render_leaf_crm_admin_page", $icon_data_uri);
	}

}
