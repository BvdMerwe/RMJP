<?php require_once('partials-wp-criteria-edit.php'); ?>
<div class="wrap">    
    <h2>
    <?php if (isset($_GET['action']) && $_GET['action'] === $this->plugin_text_domain.'_duplicate') {_e('Duplicate Category', $this->plugin_text_domain);}
    elseif(isset($_GET['id'])){_e('Edit Category', $this->plugin_text_domain);}  
    else {_e('Add Category', $this->plugin_text_domain);} ?>
    </h2>
<br/>
    <div id="rmjp-categories">			
        <div id="rmjp-post-body">		
            <form id="rmjp-category-edit-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">					
                
            <input type="hidden" name="action" value="<?php echo $this->plugin_text_domain.'_post_category'; ?>">
            <?php //var_dump($rm_forms) ?>
            <?php //var_dump($category) ?>


            <?php ((isset($_GET['action']) && $_GET['action'] === $this->plugin_text_domain.'_duplicate') || !isset($_GET['id'])) ? wp_nonce_field( 'add_category' ) : wp_nonce_field( 'edit_category_'.$_GET['id'] ) ; ?>
            <input required type="hidden" name="id" id="id" value="<?php echo ((isset($_GET['action']) && $_GET['action'] === $this->plugin_text_domain.'_duplicate') || !isset($_GET['id'])) ? '0' : $_GET['id']; ?>"/>
            <h2>Category Details</h2>
            <fieldset id="">
                <label for="name">Category Name</label>
                <br/>
                <input required type="text" name="name" id="name" value='<?php echo (isset($_GET['id'])) ? $category['name'] : ''; ?>' />
                <br/>
                <br/>
                <label for="shortcode">Shortcode</label>
                <br/>
                <input required type="text" name="shortcode" id="shortcode"value='<?php echo (isset($_GET['id'])) ? $category['shortcode'] : ''; ?>' />
                <br/>
                <br/>
                <label for="form_id">Entry Form</label>
                <br/>
                <select required name="form_id">
                    <?php foreach ($rm_forms as $form) : ?>

                    <option value='<?php echo $form['form_id'] ;?>' <?php echo (isset($_GET['id']) && $category['form_id'] ===$form['form_id']) ? 'selected' : '';?>><?php echo $form['form_name'];?></option>
                        
                    <?php endforeach;?>
                </select>
                <br/>
                <br/>

                <label for="start_time">Start Date</label>
                <br/>
                <input required type="date" name="start_time" id="start" value='<?php echo date('Y-m-d', strtotime($category['start_time'])); ?>' />
                <br/>
                <br/>

                <label for="end_time">End Date</label>
                <br/>
                <input required type="date" name="end_time" id="end" value='<?php echo date('Y-m-d', strtotime($category['end_time'])); ?>' />
            </fieldset>
<br/>
            <!-- may as well put some of the criteria here -->
            <br class='divider'/>
            <h2>Criteria</h2>
            <p>Criteria is what the judges will provide to judge/rate the entry.</p>
            <sub>Add or remove criteria with the [+] and [-] buttons</sub>
            <fieldset id="criteria">
            <style>
                #criteria_hidden {
                    display: none;
                }
            </style>
            <?php 
            echo criteria_edit('hidden', null, $rmjp_types, $this->plugin_text_domain);

            foreach($rmjp_criteria as $index => $crit) : 
                echo criteria_edit($index, $crit, $rmjp_types, $this->plugin_text_domain);
            endforeach; 
            ?>
            
            <a href='javasctipt:void()' class="add monotype">[+]</a>
            
            </fieldset>

            <!-- end criteria -->
<br/>

            <button type='submit' class='button button-primary'>Save</button>
            </form>
        </div>			
    </div>
</div>
<script>
// var shouldUpdateShortcode = true;
// var shortcode = '';
// var name = '';
// var scInput = document.querySelector('#shortcode');
// var nameInput = document.querySelector('#name');
// var id = document.querySelector('#id').value;

// nameInput.addEventListener('input', function (e) {
//     name = this.value;
//     shortcode = (name+'-'+id).toLowerCase();

//     scInput.value = shortcode;
// })
var criteriaType = document.querySelectorAll('[id^="criteria_type"]');

for (var i=0; i < criteriaType.length; i++) {
    var critInput = criteriaType[i];
    critInput.addEventListener('input', function (e) {
        if (this.value === "option" ||
            this.value === "checkbox" ||
            this.value === "radio") {
            this.parentElement.querySelector('.options').disabled = false;
        } else {
            this.parentElement.querySelector('.options').disabled = true;
        }
    })    
}

//add fields
// var additionalFields = document.querySelector('#criteria').children.length-3;
var cloneField = document.querySelector('#criteria_title_hidden')
document.querySelector('a.add').addEventListener('click', function (e) {
    var additionalFields = document.querySelector('#criteria').children.length-3;
    var field = cloneField.parentNode.cloneNode(true);
    field.id = 'criteria_'+additionalFields;
    //update attrs
    var id = field.querySelector('#criteria_id_hidden');
    var title = field.querySelector('#criteria_title_hidden');
    var type = field.querySelector('#criteria_type_hidden');
    var tooltip = field.querySelector('#criteria_tooltip_hidden');
    var option = field.querySelector('#criteria_option_hidden');

    id.disabled = false;
    id.name = 'criteria_id['+additionalFields+']';
    id.id = 'criteria_id_-'+additionalFields;

    title.disabled = false;
    title.name = 'criteria_title['+additionalFields+']';
    title.id = 'criteria_title_-'+additionalFields;

    type.disabled = false;
    type.name = 'criteria_type['+additionalFields+']';
    type.id = 'criteria_type_-'+additionalFields;

    tooltip.disabled = false;
    tooltip.name = 'criteria_tooltip['+additionalFields+']';
    tooltip.id = 'criteria_tooltip_-'+additionalFields;

    // option.disabled = false;
    // option.name = 'criteria_option['+additionalFields+']';
    // option.id = 'criteria_option_-'+additionalFields;

    this.parentNode.insertBefore(field, this);
});

//remove fields
document.addEventListener('click', function (e) {
    
    if(e.target && e.target.classList.contains('remove')){
        if (window.confirm('Removing this criteria will delete all judge submissions for this criteria.\n\n Reccomendation: Disregard the submissions for this criteria')){
            e.target.parentNode.parentNode.removeChild(e.target.parentNode);
        }
    }
});

</script>