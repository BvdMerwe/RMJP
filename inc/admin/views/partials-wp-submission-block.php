<?php 
    function submission_block($submission, $text_domain) {
        // var_dump($submission);
        $index = 0;
?>

    <tr>

        <!-- submission id: <?php echo $submission['id']; ?> -->

        <?php for ($i = 0; $i < 2 && $i <= count($submission['data']); $i++) {
            $data = $submission['data'][$i];
        ?>
        <td>
        <strong><?php echo $data['label']; ?>:</strong>
        <br/>
        <?php echo $data['value']; ?>
        <br/>
        <br/>
        </td>
        <?php 
        }
        ?>
        <td>
        <?php 
        $query_args_judge_submission = array(
            'page'		=>  $text_domain.'_judge_category',
            'category_id'	=> absint( $_GET['category_id']),
            'submission_id'	=> absint( $submission['id']),
        );
        // judge link here
        echo "<a href=".esc_url( add_query_arg( $query_args_judge_submission, admin_url( 'admin.php' ) ) ).">Rate Submission</a>";
        ?>
        </td>
    </tr>   

<?php
    }
?>