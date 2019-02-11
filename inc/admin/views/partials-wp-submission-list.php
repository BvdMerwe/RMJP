<?php
    include_once( 'partials-wp-submission-block.php' );
?>
<div class="wrap">
    <h2>
    <?php _e('Submissions for '.$judge->get_category()['name'], $this->plugin_text_domain); ?>
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
            foreach ($rmjp_submissions as $sub) {
                submission_block($sub, $this->plugin_text_domain);
            }
            ?>

            </tbody>
        </table>
    </div>
</div>