<?php require_once('partials-wp-criteria-edit.php'); ?>
<div class="wrap">    
    <h2>
    <?php if (isset($_GET['action']) && $_GET['action'] === $this->plugin_text_domain.'_duplicate') {_e('Duplicate Category', $this->plugin_text_domain);}
    elseif(isset($_GET['id'])){_e('Judge', $this->plugin_text_domain);}  
    else {_e('Add Category', $this->plugin_text_domain);} ?>
    </h2>
<br/>
    <div id="rmjp-categories">			
        <div id="rmjp-post-body">		
            <form id="rmjp-category-edit-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">					
                
            <input type="hidden" name="action" value="<?php echo $this->plugin_text_domain.'_post_judge_category'; ?>">
            <?php //var_dump($rm_forms) ?>
            <?php //var_dump($category) ?>

            <input required type="hidden" name="id" id="id" value="<?php echo (!isset($_GET['id'])) ? '0' : $_GET['id']; ?>"/>
            
            <?php
                $judge->build_page($category);
            ?>

            <button type='submit' class='button button-primary'>Save</button>
            </form>
        </div>			
    </div>
</div>
<script>
</script>