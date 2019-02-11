<?php

namespace Registration_Magic_Judging_Platform\Inc\Core;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       http://bernardus.co.za
 * @since      1.0.0
 *
 * @author     Bernard van der Merwe
 **/
class Deactivator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		Deactivator::die_db_tables();
	}

	/**
	 * Remove the Created Tables
	 * 
	 * @since    1.0.0
	 */
	public static function die_db_tables() {
		global $wpdb;
		global $rmjp_db_version;
		// if uninstall.php is not called by WordPress, die
		// if (!defined('WP_UNINSTALL_PLUGIN')) {
		// 	die('you have no power here!');
		// }
		// TODO: Remove Tables
		// $category_table = $wpdb->prefix . "rmjp_category"; 
		// $criteria_table = $wpdb->prefix . "rmjp_criteria"; 
		// $data_table = $wpdb->prefix . "rmjp_data"; 
		// $sql = "DROP TABLE $category_table, $criteria_table, $data_table";
		// $wpdb->query($sql);
		// add_option( 'rmjp_db_version', $rmjp_db_version );
	}

}
