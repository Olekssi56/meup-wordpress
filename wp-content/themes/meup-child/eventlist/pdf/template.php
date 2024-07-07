<?php
// Get Info ticket
$ticket = $args['ticket'];
?>

<table class="pdf_content">
    <tbody>
    <tr style="border: 5px solid <?php echo $ticket['color_border_ticket'] ?>;">
        <!-- ticket logo and QR code -->
        <td class="left">
            <table style="border: none;">
                <!-- ticket logo -->
                <tr>
                    <td class="horizontal_center">
						<?php if( $ticket['logo_url'] ){ ?>
                            <img src="<?php echo esc_url($ticket['logo_url']); ?>" width="150" />
						<?php } ?>
                    </td>
                </tr>
                <br><br>
                <!-- QR code -->
                <tr>
                    <td class="horizontal_center">
                        <!-- You can change size to 1.1, 1.2, 2 -->
                        <barcode code="<?php echo $ticket['qrcode_str']; ?>" type="QR" disableborder="1" size="2" />
						<?php if( apply_filters( 'el_show_qrcode_pdf_ticket', true ) ){ ?>
                            <br><br><br>
							<?php echo $ticket['qrcode_str']; ?>
						<?php } ?>
                    </td>
                </tr>
            </table>
        </td>
        <!-- ticket info -->
        <td class="right">
            <table style="border: none; width: 350px" vertical-align="top">
                <!-- Price -->
                <tr>
                    <td class="price">N<?php echo number_format($ticket['price_ticket']); ?></td>
                </tr>
                <br><br>
                <!-- Event name -->
                <tr><td class="event_name"><?php echo $ticket['event_name']; ?></td></tr>
                <br>
                <!-- Date and time -->
                <tr><td class="date"><?php echo $ticket['date']; ?> // <?php echo $ticket['time']; ?></td></tr>
                <br><br>
                <!-- Logo -->
				<?php if(count($ticket['badge_urls']) > 0): ?>
                    <tr>
                        <td style="width: 350px; vertical-align: center; text-align: center;">
                            <table>
                                <tr>
                                    <td><img style="margin-left: 10px" src="<?php echo $ticket['badge_urls'][0];?>" alt="Logo 3" width="100px"></td>
                                    <td>
                                        <div>
											<?php if(count($ticket['badge_urls']) > 1): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][1];?>" alt="Logo 1" width="70">
											<?php endif;?>
											<?php if(count($ticket['badge_urls']) > 2): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][2];?>" alt="Logo 2" width="70">
											<?php endif;?>
                                        </div>
                                        <div>
											<?php if(count($ticket['badge_urls']) > 3): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][3];?>" alt="Logo 4" width="70">
											<?php endif;?>
											<?php if(count($ticket['badge_urls']) > 4): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][4];?>" alt="Logo 5" width="70">
											<?php endif;?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        <td>
                    </tr>
				<?php endif;?>
            </table>
        </td>
    </tr>
    </tbody>
</table>

<!-- Description Ticket -->
<p style="color: <?php echo apply_filters( 'el_desc_ticket_pdf', '#333333' ); ?>">
	<?php echo $ticket['desc_ticket']; ?>
</p>

<!-- Private Ticket -->
<p style="color: <?php echo apply_filters( 'el_private_desc_ticket_pdf', '#333333' ); ?>">
	<?php echo $ticket['private_desc_ticket']; ?>
</p>

<style>
    table.pdf_content{
        border-collapse: collapse;
        border: 0px solid <?php echo $ticket['color_border_ticket'] ?>;
        color: <?php echo $ticket['color_label_ticket']; ?>;
    }
    .left{
        width: 300px;
        padding: 15px;
        justify-content: center;
        align-items: center;
    }
    .right{
        width: 350px;
        padding: 0px;
    }
    .horizontal_center{
        text-align: center;
    }
    .price{
        font-size: 30px;
        font-weight: bold;
        text-align: center;
    }
    .event_name{
        font-size: 20px;
        text-align: center;
    }
    .date{
        font-size: 15px;
        text-align: center;
    }
    .child-logo {
        vertical-align: middle;
        margin: 0 10px;
    }
</style>


