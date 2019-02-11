<?php 
    function submission_block($submission) {
        // var_dump($submission);
        $index = 0;
?>

    <tr>

        <!-- submission id: <?php echo $submission['id']; ?> -->

        <?php foreach ($submission['data'] as $data) : ?>
        <td>
        <?php echo $data['label'] ?>:
        <br/>
        <?php echo $data['value'] ?>
        <br/>
        <br/>
        </td>
        <?php 
        endforeach;
        ?>
        <td>
        
        </td>
    </tr>   

<?php
    }
?>