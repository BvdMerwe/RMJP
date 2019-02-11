<?php
    include_once( 'partials-wp-submission-block.php' );
?>
<div class="wrap">
    <h2>
    <?php _e('Entries for '.$category->get_category()['name'], $this->plugin_text_domain); ?>
    </h2>
    <br/>
    <div class="submission-format">

        <table class='wp-list-table widefat fixed striped entries'>
            <!-- <thead>
                <tr>
                    <th>Entry Name</th>
                </tr>
            </thead> -->
            <tbody id="the-list">

            <?php
            foreach ($rm_submissions as $sub) {
                submission_block($sub);
            }
            ?>

            </tbody>
        </table>
    </div>
</div>