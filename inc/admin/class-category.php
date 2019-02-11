<?php
namespace Registration_Magic_Judging_Platform\Inc\Admin;

/**
 * Class for displaying registered WordPress Users
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class Category {
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

    /**
     * Array of Criteria
     */
    protected $criteria;
	
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
    $this->name = '';
    $this->form_id = '';
    $this->shortcode = '';
    $this->start_time = '';
    $this->end_time = '';
    $this->criteria = array();
  }

  public function build_category($id, $name, $form_id, $shortcode, $start_time, $end_time) {
    $this->id = $id;
    $this->name = $name;
    $this->form_id = $form_id;
    $this->shortcode = $shortcode;
    $this->start_time = $start_time;
    $this->end_time = $end_time;

    //get criteria
    if ($this->id) {
      $this->init_criteria();
    }
  }

  public function build_category_from_array($array) {
    $this->id = $array['id'];
    $this->name = $array['name'];
    $this->form_id = $array['form_id'];
    $this->shortcode = $array['shortcode'];
    $this->start_time = $array['start_time'];
    $this->end_time = $array['end_time'];

    //get criteria
    if ($this->id) {
      $this->init_criteria();
    }
  }

  public function build_category_from_id($id) {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_category';		

    $user_query = "SELECT 
                      id, name, form_id, shortcode, start_time, end_time
                    FROM 
                      $wpdb_table 
                    WHERE id = $id";

    $result = $wpdb->get_results( $user_query, ARRAY_A  )[0];

    if (!$result) {
        return 'error';
    }
    // var_dump($result);
    $this->id = $result['id'];
    $this->name = $result['name'];
    $this->form_id = $result['form_id'];
    $this->shortcode = $result['shortcode'];
    $this->start_time = $result['start_time'];
    $this->end_time = $result['end_time'];

    //get criteria
    if ($this->id) {
      $this->init_criteria();
    }
  }

  public function init_criteria() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix  . 'rmjp_criteria';		

    $user_query = "SELECT 
            *
            FROM 
            $wpdb_table 
            WHERE category_id = ".$this->id;

    $result = $wpdb->get_results( $user_query, ARRAY_A  );
    
    foreach ($result as $key => $criteria) {
      $crit = new Criteria($this->plugin_text_domain);
      $crit->build_criteria_from_array($criteria);

      array_push($this->criteria, $crit);
      // var_dump($crit);
      // echo '<br><br>';
    }
  }

  /**
   * Update Criteria to category from array of criteria (assume there is no ID)
   */
  public function update_criteria($array) {
    $this->criteria = $array;


  }

  public function get_category() {
    return array(
      "id" => $this->id,
      "name" => $this->name,
      "form_id" => $this->form_id,
      "shortcode" => $this->shortcode,
      "start_time" => $this->start_time,
      "end_time" => $this->end_time,
      "criteria" => $this->criteria,
    );
  }

  public function validate() {
    if (
      $this->id == '' ||
      $this->name == '' ||
      $this->form_id == '' ||
      $this->shortcode == '' ||
      $this->start_time == '' ||
      $this->end_time == ''
    ) {
      return false;
    }
    return true;
  }

  public function insert_category() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_category';		
    $catVals = $this->get_category();
    unset($catVals['criteria']);

    $wpdb->insert(
      $wpdb_table,
      $catVals
    );

    //insert criteria
    foreach ($this->criteria as $crit) {
      $crit->insert_criteria_with_id($wpdb->insert_id);
    }
  }

  public function update_category() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'rmjp_category';		
    $catVals = $this->get_category();
    unset($catVals['criteria']);

    $wpdb->update(
      $wpdb_table,
      $catVals,
      array('id' => $this->id)
    );

    //insert or update criteria
    foreach ($this->criteria as $crit) {
      $critVals = $crit->get_criteria();
      if ($critVals['id'] && $critVals['id'] != '') {
        $crit->update_criteria();
      } else {
        $crit->insert_criteria_with_id($this->id);
      }
    }
  }

  
  
}