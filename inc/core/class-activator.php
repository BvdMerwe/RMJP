<?php

namespace Registration_Magic_Judging_Platform\Inc\Core;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       http://bernardus.co.za
 * @since      1.0.0
 *
 * @author     Bernard van der Merwe
 **/
class Activator {

	/**
	 * Activate the Plugin.
	 *
	 * Call activation functions - Initialise the DB.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

			$min_php = '5.6.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}

		Activator::init_db_tables();

	}

	/**
	 * Initialise the Database tables.
	 *
	 * @since    1.0.0
	 */
	private static function init_db_tables() {
		global $wpdb;
		global $award_judging_db_version;
		//Create Data Tables
		/*
		judging_category
			id
			form_id
			category_info
				name
				shortcode
				start_date
				end_date
				category?
		*/
		$table_name = $wpdb->prefix . "award_judging_category"; 
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		-- time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		form_id mediumint(9) NOT NULL,
		start_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		end_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		shortcode varchar(255) NOT NULL,
		category varchar(255) NOT NULL,
		name varchar(255) NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		/*
		judging_criteria
			id
			judging_page_id
			criteria_type
			criteria_title
			?criteria_options []
			?
		*/
		$table_name = $wpdb->prefix . "award_judging_criteria"; 
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		judging_page_id mediumint(9) NOT NULL,  #link to page table
		criteria_type varchar(255) NOT NULL,    #type to display in editing and viewing - text, shorttext, option, checkbox, radio, number, etc
		criteria_title varchar(255) NOT NULL,   #Title to display for the criteria
		criteria_tooltip varchar(255) NOT NULL, #Tooltip to display for the criteria
		criteria_options varchar(255),          #php array of options for possible dropdown/checkbox/radio
		PRIMARY KEY  (id)
		) $charset_collate;";
	
		dbDelta( $sql );
		/*
		judging_data
			id
			judging_criteria_id
			judging_value
			
		*/
	
		$table_name = $wpdb->prefix . "award_judging_data"; 
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		judging_criteria_id mediumint(9) NOT NULL,  #link to page table
		judging_value varchar(255) NOT NULL,        #value to display
		PRIMARY KEY  (id)
		) $charset_collate;";
	
		dbDelta( $sql );
		
		add_option( 'award_judging_db_version', $award_judging_db_version );
	}

}
