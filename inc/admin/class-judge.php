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
    protected $category;
    protected $submission;


	
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

  public function build_judge_submissions() {
		global $wpdb;
    $this->category = new Category($this->plugin_text_domain);
    $this->category->build_category_from_id($_GET['category_id']);
    $rm_submissions = array();

    //get category field ID
    $wpdb_table = $wpdb->prefix  . 'rm_fields';

    $user_query = "SELECT 
            field_id
            FROM 
            $wpdb_table 
            WHERE form_id = ".$this->get_category()['form_id']."
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
            WHERE form_id = ".$this->get_category()['form_id']."
            AND field_id LIKE '$rm_category_field_id'
            AND value LIKE '".$this->get_category()['name']."'";

    $result = $wpdb->get_results( $user_query, ARRAY_A  );

    if (!$result) {
      die('No submissions for this category yet');
    }
    $rmjp_submission_id = $result;

    //get submissions
    $wpdb_table = $wpdb->prefix  . 'rm_submissions';

    $user_query = "SELECT 
            submission_id, data
            FROM 
            $wpdb_table 
            WHERE submission_id LIKE '".$rmjp_submission_id[0]["submission_id"]."'";

    foreach ($rmjp_submission_id as $key => $sub_id) {
      if ($key === 0) { continue; }
      $user_query .= "
                      OR submission_id LIKE '".$sub_id["submission_id"]."'";
    }

    $rmjp_submissions = $wpdb->get_results( $user_query, ARRAY_A  );

    foreach ($rmjp_submissions as $sub) {
      $submission = $this->build_submission($sub);
      array_push($rm_submissions, $submission);
    }
    // var_dump($result);
    return $rm_submissions;
  }

  protected function build_submission($sub) {
    $submission = array();
    $submission["id"] = $sub["submission_id"];
    $submission["data"] = array();

    $submission_data = unserialize($sub['data']);

    foreach ($submission_data as $field) {
      $field = json_decode(json_encode($field), true);
      array_push($submission['data'], $field);
    }

    return $submission;
  }

  public function build_judge_submission() {
    global $wpdb;
    $this->category = new Category($this->plugin_text_domain);
    $this->category->build_category_from_id($_GET['category_id']);

    //get submission
    $wpdb_table = $wpdb->prefix  . 'rm_submissions';

    $user_query = "SELECT 
            submission_id, data
            FROM 
            $wpdb_table 
            WHERE submission_id LIKE '".$_GET['submission_id']."'";

    
    $submissions = $wpdb->get_results( $user_query, ARRAY_A  );

    
    $submission = $this->build_submission($submissions[0]);

    $this->submission = $submission;
  }

  public function build_page() {
   //get category criteria
  //  var_dump($this->category->get_category()["criteria"]);

    foreach ($this->category->get_category()["criteria"] as $key => $crit) {
      $criteria = $crit->get_criteria();
      // print_r($criteria);echo "<br/>";
?>

     <input type="hidden" name="criteria_id" value="<?php echo $criteria["id"]; ?>" />
     <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>" />

     <!-- <h2><label for="<?php echo $key; ?>"> <?php echo $criteria["criteria_title"]; ?>  </label></h2> -->

<?php
      echo $this->page_part($criteria);

    }
    echo "<br/><br/>";
  }

  //return correct html for the provided Criteria
  public function page_part($criteria) {
    if (!is_array($criteria)) {
      $criteria = $criteria->get_criteria();
    }
    
    $html_string = "<h4><label for=".$criteria['id'].">".$criteria['criteria_title']."</label></h4>";
    switch ($criteria['criteria_type']) {
      case "text":
        $html_string .= "<textarea rows='10' style='width:100%;' name='".$criteria['id']."' required placeholder='".$criteria['criteria_tooltip']."'></textarea>";
        break;
      case "number":
        $html_string .= "<input type='number' style='width:100%;' name='".$criteria['id']."' required placeholder='".$criteria['criteria_tooltip']."' />";
        break;
      case "dropdown":
      case "checkbox":
      default:
        $html_string .= '<i>Invalid Type <strong>\''.$criteria['criteria_type'].'\'</strong></i>';
        break;
    }

    return $html_string;
  }

  public function get_category() {
    return $this->category->get_category();
  }

  public function get_submission() {
    return $this->submission;
  }

  public function get_judge() {
    return array(
    );
  }

  
  
}