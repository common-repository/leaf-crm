<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.leaf.my
 * @since      1.0.0
 *
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
 * @author     Leaf CRM Team <developer@leaf.my>
 */
class Leaf_Crm_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Subscribe to Leaf CRM if Leaf Token previously saved
		$Leaf_Crm_api = new Leaf_Crm_API();
		$Leaf_Crm_api->subscribe_leaf_crm(); 
	}

}
