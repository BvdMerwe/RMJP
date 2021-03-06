<?php
namespace Registration_Magic_Judging_Platform\Inc\Admin;
use Registration_Magic_Judging_Platform\Inc\Libraries;

/**
 * Class for displaying registered WordPress Users
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class Categories_List_Table extends Libraries\WP_List_Table {
  /**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
  protected $plugin_text_domain;
  protected $is_judge;
	
  /*
  * Call the parent constructor to override the defaults $args
  * 
  * @param string $plugin_text_domain	Text domain of the plugin.	
  * 
  * @since 1.0.0
  */
  public function __construct( $plugin_text_domain ) {
    
    $this->plugin_text_domain = $plugin_text_domain;
    $this->is_judge = ($_GET['page'] == $this->plugin_text_domain.'_judge_category');
    
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
      'cb'		=> '<input type="checkbox" />', // to display the checkbox.			 
      'name'	=> __( 'Category Name', $this->plugin_text_domain ),
      'end_time'	=> __( 'Deadline', $this->plugin_text_domain ),
      'criteria'	=> __( 'Criteria', $this->plugin_text_domain ),
    );		
    return $table_columns;		   
  }	
  
  /**
	 * Provides feedback if there are no data items.
	 *
	 * @since    1.0.0
	 */
  public function no_items() {
    _e( 'No categories avaliable.', $this->plugin_text_domain );
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
    $categories_per_page = $this->get_items_per_page( 'categories_per_page' );
    $table_page = $this->get_pagenum();		
    // provide the ordered data to the List Table
    // we need to manually slice the data based on the current pagination
    $this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $categories_per_page ), $categories_per_page );
    // set the pagination arguments		
    $total_users = count( $table_data );
    $this->set_pagination_args( array (
      'total_items' => $total_users,
      'per_page'    => $categories_per_page,
      'total_pages' => ceil( $total_users/$categories_per_page )
    ) );
  }

  public function fetch_table_data() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_category';		
    $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'form_id';
    $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';

    $user_query = "SELECT 
                      cat.id, cat.name, cat.end_time, cat.form_id, GROUP_CONCAT(crit.criteria_title ORDER BY crit.id) AS criteria
                    FROM 
                      $wpdb_table cat
                    LEFT OUTER JOIN
                      wp_rmjp_criteria crit
                    ON
                      cat.id = crit.category_id";

    //check if judging page and add query
    if ($this->is_judge) {
      $user_query .= "
      WHERE cat.end_time > NOW()";
    }
    //finish query with ordering
    $user_query .= "
    GROUP BY cat.id
    ORDER BY $orderby $order";

    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results( $user_query, ARRAY_A  );
    
    //get form name and append to $query_results
    $wpdb_table = $wpdb->prefix . 'rm_forms';
    foreach ($query_results as $key => $res) {
      $user_query = "SELECT 
                        form_name
                      FROM
                        $wpdb_table
                      WHERE
                        form_id = ".$query_results[$key]["form_id"];
      
      $query_results[$key] = array_merge($res, $wpdb->get_results( $user_query, ARRAY_A  )[0]);
    }

    // return result array to prepare_items.
    // var_dump($query_results);
    return $query_results;		
  }	
  
  /*
  * Method for rendering the user_login column.
  * Adds row action links to the user_login column.
  * e.g. url/users.php?page=nds-wp-list-table-demo&action=edit_category&user=18&_wpnonce=1984253e5e
  */
  protected function column_name( $item ) {		
    $activedate = strtotime($item["end_time"]) > strtotime('today');

    $admin_page_url =  admin_url( 'admin.php' );
    // row action to view usermeta.
    $query_args_judge_category = array(
      'page'		=>  $this->plugin_text_domain.'_judge_category',
      // 'action'	=> $this->plugin_text_domain.'_edit_category',
      'category_id'	=> absint( $item['id']),
    );
    $query_args_view_category = array(
      'page'		=>  $this->plugin_text_domain.'_view_category',
      // 'action'	=> $this->plugin_text_domain.'_edit_category',
      'id'	=> absint( $item['id']),
      '_wpnonce'	=> wp_create_nonce( 'view_category_nonce' ),
    );
    $query_args_edit_category = array(
      'page'		=>  $this->plugin_text_domain.'_edit_category',
      // 'action'	=> $this->plugin_text_domain.'_edit_category',
      'id'	=> absint( $item['id']),
      '_wpnonce'	=> wp_create_nonce( 'edit_category_nonce' ),
    );
    $query_args_duplicate_category = array(
      'page'		=>  $this->plugin_text_domain.'_edit_category',
      'action'		=>  $this->plugin_text_domain.'_duplicate',
      // 'action'	=> $this->plugin_text_domain.'_edit_category',
      'id'	=> absint( $item['id']),
      '_wpnonce'	=> wp_create_nonce( 'duplicate_category_nonce' ),
    );
    
    $judge_category_link = esc_url( add_query_arg( $query_args_judge_category, $admin_page_url ) );		
    $view_category_link = esc_url( add_query_arg( $query_args_view_category, $admin_page_url ) );		
    $edit_category_link = esc_url( add_query_arg( $query_args_edit_category, $admin_page_url ) );		
    $duplicate_category_link = esc_url( add_query_arg( $query_args_duplicate_category, $admin_page_url ) );		
    $actions = array();
    if ($activedate) {
      $actions['judge_category'] = '<a href="' . $judge_category_link . '">' . __( 'View Entries', $this->plugin_text_domain ) . '</a>';		 
    }
    //if not judging page
    if (!$this->is_judge) {
      $actions['view_category'] = '<a href="' . $view_category_link . '">' . __( 'View Results', $this->plugin_text_domain ) . '</a>';		
      $actions['edit_category'] = '<a href="' . $edit_category_link . '">' . __( 'Edit', $this->plugin_text_domain ) . '</a>';		
      $actions['duplicate_category'] = '<a href="' . $duplicate_category_link . '">' . __( 'Duplicate', $this->plugin_text_domain ) . '</a>';		
      
      $row_value = '<strong><a href="' . $view_category_link . '">' . $item['name']  . '</a></strong>';
    } else {
      $row_value = '<strong>' . $item['name']  . '</a></strong>';
    }
    $row_value .= '&nbsp;<strong><i>~'.$item["form_name"].'</i></strong>';
    $row_value .= ($activedate) ? '' : '&nbsp;<strong><i>~Inactive</i></strong>';

    return $row_value . $this->row_actions( $actions );
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

  /**
   * Get value for checkbox column.
   *
   * @param object $item  A row's data.
   * @return string Text to be placed inside the column <td>.
   */
  protected function column_cb( $item ) {
    return sprintf(		
    '<label class="screen-reader-text" for="category_' . $item['id'] . '">' . sprintf( __( 'Select %s' ), $item['name'] ) . '</label>'
    . "<input type='checkbox' name='category[]' id='category_{$item['id']}' value='{$item['id']}' />"					
    );
  }

  protected function get_sortable_columns() {
    /*
     * actual sorting still needs to be done by prepare_items.
     * specify which columns should have the sort icon.	
     */
    $sortable_columns = array (
        'id' => array( 'id', true ),
        'name'=>'name',
        'end_time'=>'end_time'
      );
    return $sortable_columns;
  }	

  // Returns an associative array containing the bulk action.
  public function get_bulk_actions() {
    /*
      * on hitting apply in bulk actions the url paramas are set as
      * ?action=bulk-download&paged=1&action2=-1
      * 
      * action and action2 are set based on the triggers above and below the table		 		    
      */

      if (current_user_can( 'edit_users' )) {
        $actions = array(
          'bulk-download' => 'Download Category Results'
        );
        
        return $actions;
      }

      return array();
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