<?php
namespace Registration_Magic_Judging_Platform\Inc\Admin;

/**
 * Class for displaying registered WordPress Users
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class Judge {
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
  }

  //return correct html for the provided Criteria
  public function page_part($criteria) {
    if (!is_array($criteria)) {
      $criteria = $criteria->get_criteria();
    }
    
    $html_string = "<label for=".$criteria['criteria_id'].">".$criteria['criteria_title']."</label>";
    switch ($criteria['criteria_type']) {
      case 'text':
        $html_string .= "<br/><input type='text' name='".$criteria['criteria_id']." required placeholder=".$criteria['criteria_tooltip']." />";
      case 'number':
        $html_string .= "<br/><input type='number' name='".$criteria['criteria_id']." required placeholder=".$criteria['criteria_tooltip']." />";
      case 'dropdown':
      case 'checkbox':
      default:
        $html_string = '<i>Invalid Type</i>';
    }

    return $html_string;
  }

  public function get_judge() {
    return array(
    );
  }

  
  
}