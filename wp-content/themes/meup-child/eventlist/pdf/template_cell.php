<?php
// Get Info ticket
$ticket_list = $args['tickets'];
$length      = count( $ticket_list );
?>
<?php
function extractHours( $timeRange ): string {
    // Split the time range into start and end times
	list( $startTime, $endTime ) = explode( ' - ', $timeRange );

    // Extract the hour from the start time
	$startTimeParts = date_parse( $startTime );
	$hour           = $startTimeParts['hour'];
	$minute         = $startTimeParts['minute'];
	$ampm           = ( $startTimeParts['hour'] >= 12 ) ? 'pm' : 'am';

    // Format the hour in 12-hour format with am/pm
	// Display the extracted time
	return ( $hour % 12) . $ampm; // Outputs: 4PM
}

?>
<div>
    <table style="">
        <tbody>
		<?php for ( $i = 0; $i < $length; $i += 2 ): ?>
            <tr>
				<?php foreach ( array_splice( $ticket_list, 0, min( 2, $length - $i ) ) as $ind => $ticket ): ?>
                    <td style="width: 400px; border-bottom: 1px solid black; border-right: <?= ( $ind == 1 ) ? "0" : "1px" ?>  solid black; ">
                        <table class="pdf_content">
                            <tbody>
                            <tr class="container">
                                <!-- ticket logo and QR code -->
                                <td class="left">
                                    <table style="border: none;">
                                        <tbody>
                                        <!-- QR code -->
                                        <tr>
                                            <td class="horizontal_center">
                                                <barcode code="<?php echo $ticket['qrcode_str']; ?>" type="QR"
                                                         disableborder="1"
                                                         size="1"/>
                                            </td>
                                        </tr>
                                        <br><br>
                                        <br>
                                    </table>
                                </td>
                                <!-- ticket info -->
                                <td class="right">
                                    <table style="border: none;" vertical-align="top">
                                        <!-- Price -->
                                        <tr>
                                            <td class="price">
                                                <span>&#8358;</span><?php echo number_format( $ticket['price_ticket'] ); ?>
                                            </td>
                                        </tr>
                                        <!-- Event name -->
                                        <tr>
                                            <td class="event_name"><?php echo $ticket['event_name']; ?></td>
                                        </tr>
                                        <!-- Date and time -->
                                        <tr>
                                            <td class="date"><?php echo $ticket['date']; ?>
                                                // <?php echo extractHours($ticket['time']); ?></td>
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
                                                                     alt="Logo 3" width="90"></td>
                                                            <td>
                                                                <div>
																	<?php if ( count( $ticket['badge_urls'] ) > 1 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][1]; ?>"
                                                                             alt="Logo 1" width="85">
																	<?php endif; ?>
																	<?php if ( count( $ticket['badge_urls'] ) > 2 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][2]; ?>"
                                                                             alt="Logo 2" width="85">
																	<?php endif; ?>
                                                                </div>
                                                                <div>
																	<?php if ( count( $ticket['badge_urls'] ) > 3 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][3]; ?>"
                                                                             alt="Logo 4" width="85">
																	<?php endif; ?>
																	<?php if ( count( $ticket['badge_urls'] ) > 4 ): ?>
                                                                        <img class="child-logo"
                                                                             src="<?php echo $ticket['badge_urls'][4]; ?>"
                                                                             alt="Logo 5" width="85">
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
        background-image: url("https://dev.seniorbarman.com/wp-content/uploads/2024/07/ticket-background-small.png");
        background-size: cover;
        background-repeat: repeat;
        background-position: left;
    }

    .container {
        color: <?php echo $ticket['color_label_ticket']; ?>;
    }

    .left {
        width: 180px;
        text-align: center;
        vertical-align: middle;
        height: 250px;
    }

    .right {
        width: 225px;
        padding: 0;
        text-align: center;
    }

    .horizontal_center {
        text-align: center;
        vertical-align: middle;
    }

    .price {
        font-size: 30px;
        font-weight: bold;
        text-align: center;
        color: <?php echo $ticket['color_label_ticket']; ?>;
    }

    .event_name {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        font-family: Arial, a, Sun-ExtA-seri, Sun-ExtA, serif;
    }

    .date {
        font-size: 16px;
        text-align: center;
        font-family: Arial, a, Sun-ExtA-seri, Sun-ExtA, serif;
    }

    .child-logo {
        vertical-align: middle;
        margin: 0 10px;
    }

    span {
        content: "\20A6";
    }
</style>


