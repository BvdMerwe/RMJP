<?php
namespace Registration_Magic_Judging_Platform\Inc\Admin;

/**
 * Class for displaying registered WordPress Users
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class Criteria {
  /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    protected $plugin_text_domain;

    protected $id;
    protected $category_id;
    protected $criteria_type;
    protected $criteria_title;
    protected $criteria_tooltip;

    /**
     * Array of Criteria Options (for checkbox, select list, radio buttons)
     * 
     * TODO: Implement
     */
    protected $criteria_options;
	
  /*
  * Call the parent constructor to override the defaults $args
  * 
  * @param string $plugin_text_domain	Text domain of the plugin.	
  * 
  * @since 1.0.0
  */
  public function __construct( $plugin_text_domain ) {
    
    $this->plugin_text_domain = $plugin_text_domain;

    $this->id = '';
    $this->category_id = '';
    $this->criteria_type = '';
    $this->criteria_title = '';
    $this->criteria_tooltip = '';
  }

  public function build_criteria($id, $category_id, $criteria_type, $criteria_title, $criteria_tooltip) {
    $this->id = $id;
    $this->category_id = $category_id;
    $this->criteria_type = $criteria_type;
    $this->criteria_title = $criteria_title;
    $this->criteria_tooltip = $criteria_tooltip;
  }

  public function build_criteria_from_array($array) {
    $this->id = $array['id'];
    $this->category_id = $array['category_id'];
    $this->criteria_type = $array['criteria_type'];
    $this->criteria_title = $array['criteria_title'];
    $this->criteria_tooltip = $array['criteria_tooltip'];
  }

  public function build_criteria_from_id($id) {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_criteria';		

    $user_query = "SELECT 
                      *
                    FROM 
                      $wpdb_table 
                    WHERE id = $id";

    $result = $wpdb->get_results( $user_query, ARRAY_A  )[0];

    if (!$result) {
        return 'error';
    }
    // var_dump($result);
    $this->id = $result['id'];
    $this->category_id = $result['category_id'];
    $this->criteria_type = $result['criteria_type'];
    $this->criteria_title = $result['criteria_title'];
    $this->criteria_tooltip = $result['criteria_tooltip'];
  }

  public function get_criteria() {
    return array(
      "id" => $this->id,
      "category_id" => $this->category_id,
      "criteria_type" => $this->criteria_type,
      "criteria_title" => $this->criteria_title,
      "criteria_tooltip" => $this->criteria_tooltip,
    );
  }

  public function validate() {
    if (
      $this->id == '' ||
      $this->category_id == '' ||
      $this->criteria_type == '' ||
      $this->criteria_title == '' ||
      $this->criteria_tooltip == ''
    ) {
      return false;
    }
    return true;
  }

  public function insert_criteria() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_criteria';		

    $wpdb->insert(
      $wpdb_table,
      $this->get_criteria()
    );
  }

  public function insert_criteria_with_id($category_id) {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_criteria';		

    $this->category_id = $category_id;

    $wpdb->insert(
      $wpdb_table,
      $this->get_criteria()
    );
  }

  public function update_criteria() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_criteria';		

    $wpdb->update(
      $wpdb_table,
      $this->get_criteria(),
      array('id' => $this->id)
    );
  }

  
  
}