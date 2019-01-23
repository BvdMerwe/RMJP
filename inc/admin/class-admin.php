<?php

namespace Registration_Magic_Judging_Platform\Inc\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://bernardus.co.za
 * @since      1.0.0
 *
 * @author    Bernard van der Merwe
 */
class Admin {

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
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;
	
	/**
	* WP_List_Table object
	*
	* @since    1.0.0
	* @access   private
	* @var      categories_list_table    $categories_list_table
	*/
   private $categories_list_table;	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param       string $plugin_name        The name of this plugin.
	 * @param       string $version            The version of this plugin.
	 * @param       string $plugin_text_domain The text domain of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

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
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/registration-magic-judging-platform-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/*
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/registration-magic-judging-platform-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_plugin_admin_menu() {

		$page_hook = add_menu_page(
			__( 'Judging Platform', $this->plugin_text_domain ), //page title
			__( 'Judging Platform', $this->plugin_text_domain ), //Menu title
			'manage_options', //capability
			$this->plugin_text_domain.'_judging_platform',
			array( $this, 'load_categories_list_table' ),
			'dashicons-star-half',
			5
		);
		
		$categories_page_hook = add_submenu_page(
			$this->plugin_text_domain."_judging_platform", //parent slug 	
			__( 'Categories', $this->plugin_text_domain ), //page title
			__( 'Categories', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_text_domain.'_judging_platform',
			array( $this, 'load_categories_list_table' )
		);

		$edit_categories_page_hook = 'rmjp_edit_category';
		
		$add_categories_page_hook = add_submenu_page(
			$this->plugin_text_domain."_judging_platform", //parent slug 	
			__( 'Add Category', $this->plugin_text_domain ), //page title
			__( 'Add Category', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_text_domain.'_judging_platform_add_category',
			array( $this, 'add_category_page' )
		);
		
		/*
		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) 
		 * 
		 * The callback below will be called when the respective page is loaded
		 * 		 
		 */				
		add_action( 'load-'.$page_hook, array( $this, '_screen_options' ) );
		add_action( 'load-'.$categories_page_hook, array( $this, '_screen_options' ) );
		add_action( 'load-'.$edit_categories_page_hook, array( $this, 'edit_categories_screen_options' ) );
		add_action( 'load-'.$add_categories_page_hook, array( $this, 'add_categories_screen_options' ) );
		
	}
	/**
	* Screen options for the Categories List Table
	*
	* Callback for the load-($page_hook_suffix)
	* Called when the plugin page is loaded
	* 
	* @since    1.0.0
	*/
	public function _screen_options() {
		$arguments = array(
			'label'		=>	__( 'Users Per Page', $this->plugin_text_domain ),
			'default'	=>	20,
			'option'	=>	'categories_per_page'
		);
		add_screen_option( 'per_page', $arguments );
		/*
		 * Instantiate the User List Table. Creating an instance here will allow the core WP_List_Table class to automatically
		 * load the table columns in the screen options panel		 
		 */	 
		$this->categories_list_table = new Categories_List_Table( $this->plugin_text_domain );		
	}
	/*
	 * Display the Categories List Table
	 * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
	 */
	public function load_categories_list_table(){
		// query, filter, and sort the data
		$this->categories_list_table->prepare_items();

		//create add link
		$query_args_edit_category = array(
			// 'page'		=>  $this->plugin_text_domain.'_edit_category',
			'action'	=> $this->plugin_text_domain.'_add_category',
			'_wpnonce'	=> wp_create_nonce( 'add_category_nonce' ),
		);
		$add_category_link = esc_url( add_query_arg( $query_args_edit_category, admin_url( 'admin.php' ) ) );		
		// render the List Table
		include_once( 'views/partials-wp-list-table-categories-display.php' );
	}
	/**
	* Screen options for the Categories Add Page
	*
	* Callback for the load-($page_hook_suffix)
	* Called when the plugin page is loaded
	* 
	* @since    1.0.0
	*/
	public function add_categories_screen_options() {
		$arguments = array(
			'label'		=>	__( 'Users Per Page', $this->plugin_text_domain ),
			'default'	=>	20,
			'option'	=>	'categories_per_page'
		);
		add_screen_option( 'per_page', $arguments );

		// include_once('views/partials-wp-categories-add.php');
		// return null;		
	}

	public function add_category_page() {
		// query, filter, and sort the data
		//$this->categories_list_table->prepare_items();
		// render the List Table
		include_once( 'views/partials-wp-categories-add.php' );
	} 

	/**
	* Screen options for the Categories Edit Page
	*
	* Callback for the load-($page_hook_suffix)
	* Called when the plugin page is loaded
	* 
	* @since    1.0.0
	*/
	public function edit_categories_screen_options() {
		// echo "yes?";
		include_once('views/partials-wp-list-table-categories-edit.php');
		// return null;		
	}
	/*
	 * Display the Categories Edit Page
	 * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
	 */
	public function load_categories_edit_page(){
		// query, filter, and sort the data
		$this->categories_list_table->prepare_items();
		// render the List Table
		include_once( 'views/partials-wp-list-table-categories-display.php' );
	}
}
