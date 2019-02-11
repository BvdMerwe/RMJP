<?php
namespace Registration_Magic_Judging_Platform\Inc\Admin;

/**
 * Class for displaying registered WordPress Users
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class CategoriesFormEdit {
  /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    protected $plugin_text_domain;

    protected $id;
    protected $name;
    protected $form_id;
    protected $shortcode;
    protected $start_time;
    protected $end_time;
	
  /*
  * Call the parent constructor to override the defaults $args
  * 
  * @param string $plugin_text_domain	Text domain of the plugin.	
  * 
  * @since 1.0.0
  */
  public function __construct( $plugin_text_domain ) {
    
    $this->plugin_text_domain = $plugin_text_domain;

    $this->$id = '';
    $this->$name = '';
    $this->$form_id = '';
    $this->$shortcode = '';
    $this->$start_time = '';
    $this->$end_time = '';
  }

  public function build_category($name, $form_id, $shortcode, $start_time, $end_time) {
    $this->$id = $id;
    $this->$name = $name;
    $this->$form_id = $form_id;
    $this->$shortcode = $shortcode;
    $this->$start_time = $start_time;
    $this->$end_time = $end_time;
  }

  public function build_category_from_id($id) {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_category';		

    $user_query = "SELECT 
                      id, name, form_id, shortcode, start_time, end_time
                    FROM 
                      $wpdb_table 
                    WHERE id = $id";

    $result = $wpdb->get_results( $user_query, ARRAY_A  );

    if (!$result) {
        return 'error';
    }
    
    $this->$id = $result['id'];
    $this->$name = $result['name'];
    $this->$form_id = $result['form_id'];
    $this->$shortcode = $result['shortcode'];
    $this->$start_time = $result['start_time'];
    $this->$end_time = $result['end_time'];
  }



  
  
}