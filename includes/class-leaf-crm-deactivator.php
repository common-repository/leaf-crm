<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.leaf.my
 * @since      1.0.0
 *
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Leaf_Crm
 * @subpackage Leaf_Crm/includes
 * @author     Leaf CRM Team <developer@leaf.my>
 */
class Leaf_Crm_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Unsubscribe from Leaf CRM
		$Leaf_Crm_api = new Leaf_Crm_API();
		$Leaf_Crm_api->unsubscribe_leaf_crm(); 
	}

}
