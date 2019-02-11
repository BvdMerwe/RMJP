<?php
namespace Registration_Magic_Judging_Platform\Inc\Admin;
use Registration_Magic_Judging_Platform\Inc\Libraries;

/**
 * Class for displaying registered WordPress Users
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class Entries_List_Table extends Libraries\WP_List_Table {
    /**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	protected $plugin_text_domain;
	
    /*
    * Call the parent constructor to override the defaults $args
    * 
    * @param string $plugin_text_domain	Text domain of the plugin.	
    * 
    * @since 1.0.0
    */
    public function __construct( $plugin_text_domain ) {
      
      $this->plugin_text_domain = $plugin_text_domain;
      
      parent::__construct( array( 
          'plural'	=>	'users',	// Plural value used for labels and the objects being listed.
          'singular'	=>	'user',		// Singular label for an object being listed, e.g. 'post'.
          'ajax'		=>	false,		// If true, the parent class will call the _js_vars() method in the footer		
        ) );
    }

    /**
	 * Get Column headers.
	 *
	 * @since    1.0.0
	 */
    public function get_columns() {		
        $table_columns = array(	 
            'name'	=> __( 'Entry Title', $this->plugin_text_domain ),
        );		
        return $table_columns;		   
    }	
    
    /**
         * Provides feedback if there are no data items.
         *
         * @since    1.0.0
         */
    public function no_items() {
        _e( 'No entries avaliable.', $this->plugin_text_domain );
    }
    
    /**
	 * Query database and Prepare items recieved. Store in List and assign to Items
	 *
	 * @since    1.0.0
	 */
    public function prepare_items() {
        //TODO: code to handle bulk actions
        $this->handle_table_actions();
        
        //used by WordPress to build and fetch the _column_headers property
        $this->_column_headers = $this->get_column_info();		      
        $table_data = $this->fetch_table_data();
        
        // code to handle data operations like sorting and filtering
        
        // start by assigning your data to the items variable
        $this->items = $table_data;	
        
        //TODO: code to handle pagination
        $entries_per_page = $this->get_items_per_page( 'entries_per_page' );
        $table_page = $this->get_pagenum();		
        // provide the ordered data to the List Table
        // we need to manually slice the data based on the current pagination
        $this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $entries_per_page ), $entries_per_page );
        // set the pagination arguments		
        $total_users = count( $table_data );
        $this->set_pagination_args( array (
        'total_items' => $total_users,
        'per_page'    => $entries_per_page,
        'total_pages' => ceil( $total_users/$entries_per_page )
        ) );
    }

    public function fetch_table_data() {
        global $wpdb;
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
        
        return $rm_submissions;
    }	

    public function column_default( $item, $column_name ) {		
        switch ( $column_name ) {			
            case 'id':
                // case 'name':
            case 'end_time':
                return $item[$column_name];
            default:
                // return $column_name;
                return $item[$column_name];
        }
    }
    public function handle_table_actions() {		
        /*
         * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
         * action - is set if checkbox from top-most select-all is set, otherwise returns -1
         * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
         */    
        if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-download' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-download' ) ) {
            $nonce = wp_unslash( $_REQUEST['_wpnonce'] );	
            /*
            * Note: the nonce field is set by the parent class
            * wp_nonce_field( 'bulk-' . $this->_args['plural'] );	 
            */
            if ( ! wp_verify_nonce( $nonce, 'bulk-users' ) ) { // verify the nonce.
                $this->invalid_nonce_redirect();
            }
            else {
                include_once( 'views/partials-wp-list-table-demo-bulk-download.php' );
                $this->graceful_exit();
            }
        }
      }
}