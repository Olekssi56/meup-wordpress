<?php
// Get Info ticket
$ticket_list = $args['tickets'];
$length      = count( $ticket_list );
?>
<div>
    <table style="">
        <tbody>
		<?php for ( $i = 0; $i < $length; $i += 3 ): ?>
            <tr>
				<?php foreach ( array_splice( $ticket_list, 0, min( 3, $length - $i ) ) as $ind => $ticket ): ?>
                    <td style="width: 33%; border-bottom: 1px solid black; border-right: <?= ( $length == $i + (int) $ind + 1 || $ind == 2 ) ? "0" : "1px" ?>  solid black; ">
                        <table class="pdf_content">
                            <tbody>
                            <tr class="container">
                                <!-- ticket logo and QR code -->
                                <td class="left">
                                    <table style="border: none;">
                                        <!-- QR code -->
                                        <tr>
                                            <td class="horizontal_center">
                                                <barcode code="<?php echo $ticket['qrcode_str']; ?>" type="QR"
                                                         disableborder="1"
                                                         size="0.8"/>
                                            </td>
                                        </tr>
                                        <br><br>
                                        <tr><td><div style="height: 50px; width: 100%;"></div></td></tr>
                                        <br><br>
                                        <tr><td><div style="height: 50px; width: 100%;"></div></td></tr>
                                    </table>
                                </td>
                                <!-- ticket info -->
                                <td class="right">
                                    <table style="border: none;" vertical-align="top">
                                        <!-- Price -->
                                        <tr>
                                            <td class="price">
                                                N<?php echo number_format( $ticket['price_ticket'] ); ?></td>
                                        </tr>
                                        <!-- Event name -->
                                        <tr>
                                            <td class="event_name"><?php echo $ticket['event_name']; ?></td>
                                        </tr>
                                        <br>
                                        <!-- Date and time -->
                                        <tr>
                                            <td class="date"><?php echo $ticket['date']; ?>
                                                // <?php echo $ticket['time']; ?></td>
                                        </tr>
                                        <br><br>
                                        <!-- Logo -->
										<?php if ( count( $ticket['badge_urls'] ) > 0 ): ?>
                                            <tr>
                                                <td style="width: 350px; vertical-align: center; text-align: center;">
                                                    <table>
                                                        <tr>
                                                            <td><img style="margin-left: 10px"
                                                                     src="<?php echo $ticket['badge_urls'][0]; ?>"
                                                                     alt="Logo 3" width="90px"></td>
                                                            <td>
                                                                <div>
																	<?php if ( count( $ticket['badge_urls'] ) > 1 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][1]; ?>"
                                                                             alt="Logo 1" width="80">
																	<?php endif; ?>
																	<?php if ( count( $ticket['badge_urls'] ) > 2 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][2]; ?>"
                                                                             alt="Logo 2" width="80">
																	<?php endif; ?>
                                                                </div>
                                                                <div>
																	<?php if ( count( $ticket['badge_urls'] ) > 3 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][3]; ?>"
                                                                             alt="Logo 4" width="80">
																	<?php endif; ?>
																	<?php if ( count( $ticket['badge_urls'] ) > 4 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][4]; ?>"
                                                                             alt="Logo 5" width="80">
																	<?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                <td>
                                            </tr>
										<?php endif; ?>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
				<?php endforeach; ?>
            </tr>
		<?php endfor; ?>
        </tbody>
    </table>
</div>
<style>
    .pdf_content {
        border-collapse: collapse;
    }

    .container {
        border: 2px solid<?php echo $ticket['color_border_ticket'] ?>;
        color: <?php echo $ticket['color_label_ticket']; ?>;
    }

    .left {
        width: 40%;
        text-align: center;
        vertical-align: middle;
        height: 300px;
    }

    .right {
        width: 60%;
        padding: 0;
        text-align: center;
    }

    .horizontal_center {
        text-align: center;
        vertical-align: middle;
    }

    .price {
        font-size: 50px;
        font-weight: bold;
        text-align: center;
        color: <?php echo $ticket['color_label_ticket']; ?>;
    }

    .event_name {
        font-size: 20px;
        text-align: center;
    }

    .date {
        font-size: 10px;
        text-align: center;
    }

    .child-logo {
        vertical-align: middle;
        margin: 0 10px;
    }
</style>


