<?php 
// Get Info ticket
$ticket_list = $args['ticket'];
$ticket = $ticket_list[0];
?>

	

	<table class="pdf_content">
		<tbody>
		  <tr style="border: 5px solid <?php echo $ticket['color_border_ticket'] ?>;">

		  	<td class="left">
		  		<table style="width: 100%; border-collapse: collapse;" >
					
					<tr class="name_event">
						<!-- Event Name -->
						<td colspan="2">
							<span style="color: <?php echo $ticket['color_label_ticket']; ?>">
								<b><?php esc_html_e( 'Event', 'eventlist' ); ?>:</b>
							</span>
							<br>
							<span style="color: <?php echo $ticket['color_content_ticket']; ?>">
								<?php echo $ticket['event_name']; ?>
							</span>
						</td>
					</tr>

					<tr class="time">
						<td class="time_content" style="border-right: 5px solid <?php echo $ticket['color_border_ticket'] ?>;">

							<div style="color: <?php echo $ticket['color_label_ticket']; ?>">
								<b><?php esc_html_e( 'Time', 'eventlist' ); ?>:</b>
							</div>

							<div style="color: <?php echo $ticket['color_content_ticket']; ?>">
								<?php echo $ticket['date']; ?>
								<br>
								<?php echo $ticket['time']; ?>
							</div>

						</td>
						<td class="venue_content" align="right">
							<div style="color: <?php echo $ticket['color_label_ticket']; ?>">
								<b><?php esc_html_e( 'Venue', 'eventlist' ); ?>:</b>
							</div>
							<div style="color: <?php echo $ticket['color_content_ticket']; ?>">
								<?php echo $ticket['venue']; ?>
								<br>
								<?php echo $ticket['address']; ?>
							</div>
					</td>
					</tr>
					
					<tr class="order_info">
						<td colspan="2">
							<div style="color: <?php echo $ticket['color_label_ticket']; ?>">
								<b><?php esc_html_e( 'Order Info', 'eventlist' ); ?>:</b>
							</div>
							<div style="color: <?php echo $ticket['color_content_ticket']; ?>">
								<?php echo $ticket['order_info']; ?>
							</div>
						</td>
					</tr>

					<tr class="ticket_type">
						<td colspan="2">
							<div style="color: <?php echo $ticket['color_label_ticket']; ?>">
								<b><?php esc_html_e( 'Ticket', 'eventlist' ); ?>:</b>
							</div>
							<div style="color: <?php echo $ticket['color_content_ticket']; ?>">
								<!-- Ticket Number -->
								#<?php echo $ticket['ticket_id']; ?> - <?php echo $ticket['type_ticket']; ?>
							</div>
						</td>
					</tr>

					<?php if ( ! empty( $ticket['extra_service'] ) ): ?>
						<tr class="extra_service">
							<td colspan="2">
								<div style="color: <?php echo $ticket['color_label_ticket']; ?>">
									<b><?php esc_html_e( 'Extra Services', 'eventlist' ); ?>:</b>
								</div>
								<div style="color: <?php echo $ticket['color_content_ticket']; ?>">
									<?php echo $ticket['extra_service']; ?>
								</div>
							</td>
						</tr>
					<?php endif; ?>

				</table>
		  	</td>

		  	<td class="right">
		  		<table style="border: none;" ertical-align="top">
		  			
					<tr>
						<td>
							<?php if( $ticket['logo_url'] ){ ?>
								<img src="<?php echo esc_url($ticket['logo_url']); ?>" width="150" />
							<?php } ?>
						</td>
					</tr>
				<br><br>
					<tr>
						<td>
							<!-- You can change size to 1.1, 1.2, 2 -->
							<barcode code="<?php echo $ticket['qrcode_str']; ?>" type="QR" disableborder="1" size="1" />
							<?php if( apply_filters( 'el_show_qrcode_pdf_ticket', true ) ){ ?>	
								<br><br><br>
								<?php echo $ticket['qrcode_str']; ?>	
							<?php } ?>
						</td>
					</tr>
					

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
	}
	
	

	.left{
		width: 500px;
		border-right: 5px solid <?php echo $ticket['color_border_ticket'] ?>;	
		padding: 0px;

	}

	.right{
		width: 150px;
		padding: 15px;
	}

	
	
	.name_event td,
	.time td,
	.order_info td
	{
		border: none;
		border-bottom: 5px solid <?php echo $ticket['color_border_ticket'] ?>;
		padding: 15px;
	}

	.ticket_type td, .extra_service td{
		padding: 15px;	
	}
	.extra_service td {
		border-top: 5px solid <?php echo $ticket['color_border_ticket'] ?>;
	}

</style>

