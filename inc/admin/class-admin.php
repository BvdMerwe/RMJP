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


	public function my_headers() {
		//TODO: NB REMOVE!!
		// header('X-XSS-Protection:0');
	}

	/**
	 * Add links to plugin page
	 */
	public function add_additional_action_link($links) {
		$links = array_merge( array(
			// '<a href="' . esc_url( admin_url( '/' ) ) . '">' . __( 'Delete Data', $this->plugin_text_domain ) . '</a>'
		), $links );
		return $links;
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
		
		$edit_categories_page_hook = $this->plugin_text_domain.'_edit_category';
		
		$add_categories_page_hook = add_submenu_page(
			$this->plugin_text_domain."_judging_platform", //parent slug 	
			__( 'Add Category', $this->plugin_text_domain ), //page title
			__( 'Add Category', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_text_domain.'_edit_category',
			array( $this, 'edit_category_page' )
		);
		

		$judge_categories_page_hook = add_submenu_page(
			$this->plugin_text_domain."_judging_platform", //parent slug 
			__( 'Judge Category', $this->plugin_text_domain ), //page title
			__( 'Judge Category', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_text_domain.'_judge_category',
			array( $this, 'judge_category_page' )
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
		add_action( 'load-'.$add_categories_page_hook, array( $this, 'edit_categories_screen_options' ) );
		add_action( 'load-'.$judge_categories_page_hook, array( $this, '_screen_options' ) );

		//form submissions
		// $post_categories_page_hook = $this->plugin_text_domain.'_post-category';
		// add_action( 'load-'.$post_categories_page_hook, array( $this, 'post_category' ) );
		
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
			'label'		=>	__( 'Categories Per Page', $this->plugin_text_domain ),
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
			'page'		=>  $this->plugin_text_domain.'_edit_category',
			// 'action'	=> $this->plugin_text_domain.'_add_category',
			'_wpnonce'	=> wp_create_nonce( 'edit_category_nonce' ),
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
	public function add_category_screen_options() {
		add_screen_option( 'help', array() );

		// include_once('views/partials-wp-categories-add.php');
		// return null;
	}

	public function judge_category_page() {
		global $wpdb;
		//if no category is selected
		if (!isset($_GET['category_id'])) {
			// query, filter, and sort the data
			$this->categories_list_table->prepare_items();

			//create add link
			$query_args_edit_category = array(
				'page'		=>  $this->plugin_text_domain.'_edit_category',
				// 'action'	=> $this->plugin_text_domain.'_add_category',
				'_wpnonce'	=> wp_create_nonce( 'edit_category_nonce' ),
			);
			$add_category_link = esc_url( add_query_arg( $query_args_edit_category, admin_url( 'admin.php' ) ) );
			// render the List Table
			include_once( 'views/partials-wp-list-table-categories-judge.php' );
		} else if (!isset($_GET['entry_id'])) { //with category ID but without entry ID

			//TODO: Display Submissions ':D
			$category = new Category($this->plugin_text_domain);
			$category->build_category_from_id($_GET['category_id']);

			//get category field ID
			$wpdb_table = $wpdb->prefix  . 'rm_fields';

			$user_query = "SELECT 
							field_id
							FROM 
							$wpdb_table 
							WHERE form_id = ".$category->get_category()['form_id']."
							AND field_label LIKE 'Category'
							AND field_type LIKE 'Select'";
	
			$result = $wpdb->get_results( $user_query, ARRAY_A  );

			if (!$result) {
				die('It appears that this category is set up with a form that does not have a Category field.');
			}

			$rm_category_field_id = $result[0]["field_id"];

			//get submission field IDs
			$wpdb_table = $wpdb->prefix  . 'rm_submission_fields';

			$user_query = "SELECT 
							submission_id
							FROM 
							$wpdb_table 
							WHERE form_id = ".$category->get_category()['form_id']."
							AND field_id LIKE '$rm_category_field_id'
							AND value LIKE '".$category->get_category()['name']."'";
	
			$result = $wpdb->get_results( $user_query, ARRAY_A  );

			if (!$result) {
				die('No entries for this category yet');
			}
			$rmjp_submission_id = $result[0]["submission_id"];

			//get submissions
			$wpdb_table = $wpdb->prefix  . 'rm_submissions';

			$user_query = "SELECT 
							submission_id, data
							FROM 
							$wpdb_table 
							WHERE submission_id LIKE '$rmjp_submission_id'";
	
			$result = $wpdb->get_results( $user_query, ARRAY_A  );
			$rmjp_submissions = $result;

			$rm_submissions = array();
			foreach ($rmjp_submissions as $sub) {
				$submission = array();
				$submission["id"] = $sub["submission_id"];
				$submission["data"] = array();

				$submission_data = unserialize($sub['data']);

				foreach ($submission_data as $field) {
					$field = json_decode(json_encode($field), true);
					array_push($submission['data'], $field);
				}
				array_push($rm_submissions, $submission);
			}

			include_once( 'views/partials-wp-submission-list.php' );
			
			// var_dump($rmjp_submissions[0]["data"]);

		} else { //with both - judging commencing
			if (!is_numeric($_GET['category_id'])) {
				//fuckin bomb the fuck out boi!
				die('You have no power here!');
			}
			$judge = new Judge($this->plugin_text_domain);
			$category = new Category($this->plugin_text_domain);
			$category->build_category_from_id($_GET['category_id']);
			
			//render page
			include_once( 'views/partials-wp-categories-judge.php' );
		}
	}

	public function edit_category_page() {

		$category = new Category( $this->plugin_text_domain );
		
		//get data from edit link
		if(isset($_GET['id'])) {
			$category->build_category_from_id($_GET['id']);
			// print_r($category);
		}
		$category = $category->get_category();
		// echo "add_category_page";
		// query, filter, and sort the data

		//get forms from registration magic
		global $wpdb;
		$wpdb_table = $wpdb->prefix . 'rm_forms';

		$user_query = "SELECT 
						form_id, form_name
						FROM 
						$wpdb_table 
						WHERE form_type = 0";

		$result = $wpdb->get_results( $user_query, ARRAY_A  );
		$rm_forms = $result;

		//create rmjp criteria types
		$rmjp_types = array(
			array(
				"type" => "text",
				"type_name" => "Text"
			),
			array(
				"type" => "number",
				"type_name" => "Number"
			),
			//TODO: Implement multi-option things
			// array(
			// 	"type" => "option",
			// 	"type_name" => "Dropdown"
			// ),
			// array(
			// 	"type" => "checkbox",
			// 	"type_name" => "Checkbox"
			// ),
			// array(
			// 	"type" => "radio",
			// 	"type_name" => "Radio Button"
			// ),
		);

		$rmjp_criteria = array();
		if ($category['id']) {
			$wpdb_table = $wpdb->prefix  . 'rmjp_criteria';

			$user_query = "SELECT 
							*
							FROM 
							$wpdb_table 
							WHERE category_id = ".$category['id'];
	
			$result = $wpdb->get_results( $user_query, ARRAY_A  );
			$rmjp_criteria = $result;
		}
		

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
		$arguments = array(
			'label'		=>	__( 'Show shortcode', $this->plugin_text_domain ),
			'default'	=>	false,
			'option'	=>	'show_shortcode'
		);
		// add_screen_option( 'per_page', $arguments );
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

	/*
	 * Post the category
	 */
	public function post_category(){

		//init error variable
		$attrs = '';

		//initialise category
		$category = new Category( $this->plugin_text_domain );

		//initialise Criteria (array)
		$criteria = array();
		// print_r($_POST);

		//init categories
		foreach ($_POST["criteria_id"] as $key => $crit_id) {
			$crit = new Criteria( $this->plugin_text_domain );
			$crit->build_criteria(
				$_POST["criteria_id"][$key],
				$_POST["id"],
				$_POST["criteria_type"][$key],
				$_POST["criteria_title"][$key],
				$_POST["criteria_tooltip"][$key]
			);
			array_push($criteria, $crit);
		}		

		//post data from form
		if (isset($_POST['_wpnonce'])) {
			// var_dump($_POST);

			//quick and dirty sanitise
			foreach ($_POST as $postkey => $postval) {
				// echo $postval;
				$_POST[$postkey] = sanitize_text_field($postval);
			}

			if ($_POST['id'] == 0) { //add new category
				check_admin_referer( 'add_category' );

				$category->build_category_from_array(shortcode_atts( $category->get_category(), $_POST, $this->plugin_text_domain ));
				$category->update_criteria($criteria);

				if ($category->validate()) {
					$category->insert_category();
				} else {
					//TODO: display error
					$attrs .= '&novalidate';
				}

			} else { //update existing category
				check_admin_referer( 'edit_category_'.$_POST['id'] );

				$category->build_category_from_array(shortcode_atts( $category->get_category(), $_POST, $this->plugin_text_domain ));
				$category->update_criteria($criteria);
				
				// var_dump(shortcode_atts( $category->get_category(), $_POST, $this->plugin_text_domain ));
				// print_r($category);
				if ($category->validate()) {
					$category->update_category();
				} else {
					//TODO: display error
					$attrs .= '&novalidate';
				}
				// print_r($category);
			}
			wp_redirect(esc_url( admin_url('admin.php') ).'?page='.$this->plugin_text_domain.'_judging_platform'.$attrs);
		}
	}
}
