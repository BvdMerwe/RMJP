
<div class="wrap">    
    <h2><?php _e('Categories', $this->plugin_text_domain);  ?><a href="<?php echo $add_category_link; ?>" class="page-title-action">Add New</a></h2>
    <div id="rmjp-categories">			
        <div id="rmjp-post-body">		
            <form id="rmjp-category-list-form" method="get">					
                <?php $this->categories_list_table->display(); ?>					
            </form>
        </div>			
    </div>
</div>