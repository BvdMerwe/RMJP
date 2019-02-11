<?php require_once('partials-wp-criteria-edit.php'); ?>
<div class="wrap">    
    <h2>
    <?php _e('Judge Submission', $this->plugin_text_domain); ?>
    </h2>
<br/>
    <div id="rmjp-categories">			
        <div id="rmjp-post-body">	
            <div class="submission-content">
            <h2>Submission Details</h2>
            <?php foreach ($judge->get_submission()['data'] as $data) : ?>
                <div style="position: relative;
                            overflow: auto;
                            margin: 16px 0;
                            padding: 10px 15px;
                            border: 1px solid #e5e5e5;
                            box-shadow: 0 1px 1px rgba(0,0,0,.04);
                            background: #fff;
                            font-size: 13px;
                            line-height: 2.1em;">
                    <strong>
                    <?php echo $data['label'] ?>:</strong>
                    <br/>
                    <?php 
                    if (filter_var( $data['value'], FILTER_VALIDATE_URL)) {
                        echo '<a href="'. $data['value'] .'" target="_blank">'. $data['value'] .'</a>';
                    } else {
                        echo $data['value'];
                    }
                    ?>
                </div>
            <?php endforeach; ?>
            </div>
            <h2>Submission Criteria</h2>
            <p>Fill in the below criteria</p>
            <form id="rmjp-category-edit-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">					
                
            <input type="hidden" name="action" value="<?php echo $this->plugin_text_domain.'_post_judge_category'; ?>">
            <?php //var_dump($rm_forms) ?>
            <?php //var_dump($category) ?>

            <!-- <input required type="hidden" name="id" id="id" value="<?php echo (!isset($_GET['id'])) ? '0' : $_GET['id']; ?>"/> -->
            
            <?php
                $judge->build_page();
            ?>

            <button type='submit' class='button button-primary'>Submit</button>
            </form>
        </div>			
    </div>
</div>
<script>
</script>