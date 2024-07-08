<?php
// Get Info ticket
$ticket_list = $args['tickets'];
?>
<div>
    <table>
        <tbody>
        <tr>
			<?php foreach ( $ticket_list as $ind => $ticket ): ?>
                <td>
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
                                                     disableborder="1" size="0.5"/>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- ticket info -->
                            <td class="right">
                                <table style="border: none; width: 350px" vertical-align="top">
                                    <!-- Price -->
                                    <tr>
                                        <td class="price">N<?php echo number_format( $ticket['price_ticket'] ); ?></td>
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
        padding: 15px 0 15px 30px;
        justify-content: center;
        align-items: center;
    }

    .right {
        width: 60%;
        padding: 0px;
    }

    .horizontal_center {
        text-align: center;
        width: 300px;
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


