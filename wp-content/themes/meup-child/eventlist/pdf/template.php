<?php
// Get Info ticket
$ticket = $args['ticket'];
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
	return ( $hour % 12 ) . $ampm; // Outputs: 4PM
}

?>
<table class="pdf_content">
    <tbody>
    <tr class="container">
        <!-- ticket logo and QR code -->
        <td class="left">
            <table style="border: none;">
                <!-- QR code -->
                <tr>
                    <td class="horizontal_center" colspan="2">
                        <barcode code="<?php echo $ticket['qrcode_str']; ?>" type="QR" disableborder="1"
                                 size="1.9"/>
                    </td>
                </tr>
            </table>
        </td>
        <!-- ticket info -->
        <td class="right">
            <table style="border: none; width: 450px" vertical-align="top">
                <tbody>
                <!-- Price -->
                <tr>
                    <td class="price"><span>&#8358;</span><?php echo number_format( $ticket['price_ticket'] ); ?></td>
                </tr>
                <!-- Event name -->
                <tr>
                    <td class="event_name"><?php echo $ticket['event_name']; ?></td>
                </tr>
                <br>
                <!-- Date and time -->
                <tr>
                    <td class="date"><?php echo $ticket['date']; ?>
                        // <?php echo extractHours( $ticket['time'] ); ?>
                    </td>
                </tr>
                <br><br>
                <!-- Logo -->
				<?php if ( count( $ticket['badge_urls'] ) > 0 ): ?>
                    <tr>
                        <td style="vertical-align: center; text-align: center;">
                            <table>
                                <tr>
                                    <td><img style="margin-left: 10px" src="<?php echo $ticket['badge_urls'][0]; ?>"
                                             alt="Logo 3" width="110px"></td>
                                    <td>
                                        <div>
											<?php if ( count( $ticket['badge_urls'] ) > 1 ): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][1]; ?>"
                                                     alt="Logo 1" width="95">
											<?php endif; ?>
											<?php if ( count( $ticket['badge_urls'] ) > 2 ): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][2]; ?>"
                                                     alt="Logo 2" width="95">
											<?php endif; ?>
                                        </div>
                                        <div>
											<?php if ( count( $ticket['badge_urls'] ) > 3 ): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][3]; ?>"
                                                     alt="Logo 4" width="95">
											<?php endif; ?>
											<?php if ( count( $ticket['badge_urls'] ) > 4 ): ?>
                                                <img class="child-logo" src="<?php echo $ticket['badge_urls'][4]; ?>"
                                                     alt="Logo 5" width="95">
											<?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        <td>
                    </tr>
				<?php endif; ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<style>
    .pdf_content {
        border-collapse: collapse;
    }

    .container {
        border: 2px solid black;
        color: <?php echo $ticket['color_label_ticket']; ?>;
        width: 700px;
    }

    .left {
        width: 250px;
        padding: 15px 0 15px 30px;
        text-align: center;
        vertical-align: middle;
        height: 300px;
    }

    .right {
        width: 450px;
        padding: 0;
    }

    .horizontal_center {
        text-align: center;
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
        font-family: Arial, a, Sun-ExtA-seri, Sun-ExtA, serif;
        font-weight: bold;
    }

    .date {
        font-size: 18px;
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


