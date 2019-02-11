<?php 
    function criteria_edit($index, $criteria, $rmjp_types, $plugin_text_domain) {
        // var_dump($criteria);
?>

<div class="criteria-format" id="criteria_<?php echo $index ?>">
    <a href='javasctipt:void()' class="remove monotype">[-]</a>
    <?php if (isset($_GET['action']) && $_GET['action'] === $plugin_text_domain.'_duplicate') : ?>
    <input type="hidden" <?php echo ($index === 'hidden') ? 'disabled' : ''; ?> name="criteria_id[<?php echo $index; ?>]" id="criteria_id_<?php echo $index; ?>" value=""/>
    <?php else :?>
    <input type="hidden" <?php echo ($index === 'hidden') ? 'disabled' : ''; ?> name="criteria_id[<?php echo $index; ?>]" id="criteria_id_<?php echo $index; ?>" value="<?php echo $criteria['id'];?>"/>
    <?php endif;?>
    <input type="text" <?php echo ($index === 'hidden') ? 'disabled' : ''; ?> name="criteria_title[<?php echo $index; ?>]" id="criteria_title_<?php echo $index; ?>" placeholder="Criteria Title" value="<?php echo $criteria['criteria_title'];?>"/>
    
    <select required <?php echo ($index === 'hidden') ? 'disabled' : ''; ?> name="criteria_type[<?php echo $index; ?>]" id="criteria_type_<?php echo $index; ?>" value="<?php echo $criteria['criteria_type'];?>">
        <option value="" disabled <?php echo ($criteria == NULL) ? ' selected ' : '';?>>Criteria type</option>
        <?php foreach ($rmjp_types as $type) : ?>

        <option value='<?php echo $type['type'] ;?>'<?php echo ($type['type']===$criteria['criteria_type']) ? ' selected ' : ''; ?>><?php echo $type['type_name'];?></option>
            
        <?php endforeach;?>
    </select>
    
    <input type="text" <?php echo ($index === 'hidden') ? 'disabled' : ''; ?> name="criteria_tooltip[<?php echo $index; ?>]" id="criteria_tooltip_<?php echo $index; ?>" placeholder="Placeholder Text" value="<?php echo $criteria['criteria_tooltip'];?>" />
    <!-- Place for addable list -->
    <!-- <input type="text" <?php echo ($index === 'hidden') ? 'disabled' : ''; ?> name="criteria_option[<?php echo $index; ?>]" id="criteria_option_<?php echo $index; ?>" placeholder="Options" class='options' disabled/> -->
    <!-- end place -->
</div>

<?php
    }
?>