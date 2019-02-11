
<div class="wrap">    
    <h2><?php _e('Judge Category', $this->plugin_text_domain);  ?></h2>
    <div id="rmjp-categories">			
        <div id="rmjp-post-body">		
            <form id="rmjp-category-list-form" method="get">					
                <?php $this->categories_list_table->display(); ?>					
            </form>
        </div>			
    </div>
</div>