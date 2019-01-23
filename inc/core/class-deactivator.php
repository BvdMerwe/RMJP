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

	}

	/**
	 * Remove the Created Tables
	 * 
	 * @since    1.0.0
	 */
	public static function die_db_tables() {
		// if uninstall.php is not called by WordPress, die
		if (!defined('WP_UNINSTALL_PLUGIN')) {
			die;
		}
		// TODO: Remove Tables
		
	}

}
