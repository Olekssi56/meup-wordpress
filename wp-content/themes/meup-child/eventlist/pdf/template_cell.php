<?php
// Get Info ticket
$ticket_list = $args['ticket_list'];
?>

<table class="pdf_content">
    <tbody>
	<?php foreach ( $ticket_list as $ind => $ticket ): ?>
        <tr>
            <td><?= $ind + 1 ?></td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>
<p>
    <?php echo var_dump($ticket_list);?>
</p>
<style>
    table.pdf_content {
        border-collapse: collapse;
        border: 0px solid<?php echo $ticket['color_border_ticket'] ?>;
        color: <?php echo $ticket['color_label_ticket']; ?>;
    }

    .left {
        width: 300px;
        padding: 15px;
        justify-content: center;
        align-items: center;
    }

    .right {
        width: 350px;
        padding: 0px;
    }

    .horizontal_center {
        text-align: center;
    }

    .price {
        font-size: 30px;
        font-weight: bold;
        text-align: center;
    }

    .event_name {
        font-size: 20px;
        text-align: center;
    }

    .date {
        font-size: 15px;
        text-align: center;
    }

    .child-logo {
        vertical-align: middle;
        margin: 0 10px;
    }
</style>


