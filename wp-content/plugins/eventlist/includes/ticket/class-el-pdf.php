<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'EL_PDF' ) ) {
	class EL_PDF {
		function make_pdf_ticket( $ticket_id ) {
			$ticket = array();

			$start_time    = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_start', true );
			$end_time      = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_end', true );
			$seat          = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'seat', true );
			$name_customer = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'name_customer', true );
			$desc_ticket   = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'desc_ticket', true );
			$venue         = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'venue', true );
			$logo_id       = get_post_meta( $ticket_id, OVA_METABOX_EVENT . "img", true );
			$person_type   = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'person_type', true );
			$extra_service = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'extra_service', true );

			// Get info ticket
			$ticket['ticket_id']  = $ticket_id;
			$ticket['event_name'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'name_event', true );

			if ( is_array( $venue ) ) {
				$ticket['venue'] = implode( ', ', $venue );
			}

			$ticket['address'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'address', true );

			$ticket['color_border_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'color_ticket', true );
			if ( $ticket['color_border_ticket'] == "#fff" || $ticket['color_border_ticket'] == "#ffffff" || empty( $ticket['color_border_ticket'] ) ) {
				$ticket['color_border_ticket'] = '#cccccc';
			}

			$ticket['color_label_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'color_label_ticket', true );
			if ( $ticket['color_label_ticket'] == "#fff" || $ticket['color_label_ticket'] == "#ffffff" || empty( $ticket['color_label_ticket'] ) ) {
				$ticket['color_label_ticket'] = '#666666';
			}

			$ticket['color_content_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'color_content_ticket', true );
			if ( $ticket['color_content_ticket'] == "#fff" || $ticket['color_content_ticket'] == "#ffffff" || empty( $ticket['color_content_ticket'] ) ) {
				$ticket['color_content_ticket'] = '#333333';
			}


			$ticket['private_desc_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'private_desc_ticket', true );
			// price ticket
			$ticket['price_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'price_ticket', true );
			//sub string
			$ticket['desc_ticket'] = sub_string_word( $desc_ticket, apply_filters( 'el_desc_ticket_characters', 230 ) );

			$ticket['date'] = date_i18n( get_option( 'date_format' ), $start_time );
			$ticket['time'] = date_i18n( get_option( 'time_format' ), $start_time ) . ' - ' . date_i18n( get_option( 'time_format' ), $end_time );

			$ticket['qrcode_str']  = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'qr_code', true );
			$ticket['type_ticket'] = $seat ? get_the_title( $ticket_id ) . ' - ' . $seat : get_the_title( $ticket_id );

			if ( $person_type ) {
				$ticket['type_ticket'] .= ' - ' . $person_type;
			}

			$ticket['order_info'] = esc_html__( 'Ordered by', 'eventlist' ) . ' ' . $name_customer;
			// Logo
			$ticket['logo_url'] = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
			// Badge URLs
			$ticket['badge_urls'] = [
				"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo3.png",
				"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo2.png",
				"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo4.png",
				"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo5.png",
				"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo1.png",
			];
			// Extra service
			$ticket['extra_service'] = el_extra_sv_ticket( $extra_service );

			$upload_dir = wp_upload_dir();

			// Add Font
			$defaultConfig = ( new Mpdf\Config\ConfigVariables() )->getDefaults();
			$fontDirs      = $defaultConfig['fontDir'];

			$defaultFontConfig = ( new Mpdf\Config\FontVariables() )->getDefaults();
			$fontData          = $defaultFontConfig['fontdata'];


			$config_mpdf = array(
				'tempDir'           => $upload_dir['basedir'],
				'default_font_size' => apply_filters( 'el_pdf_font_size_' . apply_filters( 'wpml_current_language', null ), 12 ),
				'default_font'      => apply_filters( 'el_pdf_font_' . apply_filters( 'wpml_current_language', null ), 'DejaVuSans' ),
				'fontDir'           => array_merge( $fontDirs, array( get_stylesheet_directory() . '/font' ) )
			);

			$attach_file = '';

			ob_start();
			el_get_template( 'pdf/template.php', array( 'ticket' => $ticket ) );
			$html = ob_get_contents();
			ob_get_clean();

			try {
				$mpdf = new \Mpdf\Mpdf( apply_filters( 'el_config_mpdf', $config_mpdf ) );
				$mpdf->WriteHTML( $html );
				$attach_file = WP_CONTENT_DIR . '/uploads/event__ticket' . $ticket_id . '.pdf';
				$mpdf->Output( $attach_file, 'F' );
			} catch ( \Mpdf\MpdfException $e ) { // Note: safer fully qualified exception name used for catch
				// Process the exception, log, print etc.
				echo $e->getMessage();
			}

			return $attach_file;
		}

		function make_pdf_invoice( $booking_id ) {
			$html       = $css = '';
			$upload_dir = wp_upload_dir();

			$config_mpdf = array(
				'tempDir'           => $upload_dir['basedir'],
				'default_font_size' => apply_filters( 'el_pdf_invoice_font_size_' . apply_filters( 'wpml_current_language', null ), 12 ),
				'default_font'      => apply_filters( 'el_pdf_invoice_font_' . apply_filters( 'wpml_current_language', null ), 'DejaVuSans' ),
				'format'            => apply_filters( 'el_pdf_invoice_format_' . apply_filters( 'wpml_current_language', null ), 'A4' ),
			);

			$attach_file = '';

			$invoices_dir  = trailingslashit( wp_upload_dir()['basedir'] ) . 'invoices';
			$invoice_files = glob( $invoices_dir . '/*.pdf' );

			if ( ! empty( $invoice_files ) && is_array( $invoice_files ) ) {
				foreach ( $invoice_files as $file ) {
					wp_delete_file( $file );
				}
			}

			if ( ! is_dir( $invoices_dir ) ) {
				wp_mkdir_p( $invoices_dir );
			}

			// PDF invoice name
			$pdf_name      = apply_filters( 'el_ft_pdf_invoice_name', 'pdf_invoice_' . $booking_id );
			$extra_service = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'extra_service', true );
			// Data
			$data = [];

			ob_start();
			el_get_template( 'pdf/invoice.css' );
			$css = ob_get_contents();
			ob_get_clean();
			$data['css'] = $css;

			// Get Data
			$data['title']        = EL()->options->invoice->get( 'invoice_pdf_title' );
			$data['shop_name']    = EL()->options->invoice->get( 'invoice_shop_name' );
			$data['shop_address'] = EL()->options->invoice->get( 'invoice_shop_address' );
			$data['footer']       = EL()->options->invoice->get( 'invoice_pdf_footer' );

			// Logo
			$data['logo_url'] = $data['logo_alt'] = '';
			$logo_id          = EL()->options->invoice->get( 'invoice_pdf_logo' );

			if ( $logo_id ) {
				$data['logo_url'] = wp_get_attachment_url( $logo_id );
				$data['logo_alt'] = get_post_meta( $logo_id, '_wp_attachment_image_alt', true );
			}

			// Customer
			$data['customer_name']    = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'name', true );
			$data['customer_phone']   = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'phone', true );
			$data['customer_email']   = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'email', true );
			$data['customer_address'] = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'address', true );

			// Booking
			$data['booking_number'] = $booking_id;
			$data['event_name']     = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'title_event', true );
			$data['event_link']     = get_permalink( get_post_meta( $booking_id, OVA_METABOX_EVENT . 'id_event', true ) );
			$data['event_calendar'] = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'date_cal', true );
			$data['payment_method'] = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'payment_method', true );
			$data['booking_status'] = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'status', true );
			$data['cart_details']   = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'cart', true );
			$data['extra_service']  = el_extra_sv_ticket_invoice( $extra_service );
			$data['subtotal']       = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'total', true );
			$data['discount']       = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'discount', true );
			$data['coupon']         = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'coupon', true );
			$data['tax']            = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'tax', true );
			$data['system_fee']     = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'system_fee', true );
			$data['total']          = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'total_after_tax', true );

			// Woo
			$order_id = get_post_meta( $booking_id, OVA_METABOX_EVENT . 'orderid', true );

			if ( $order_id ) {
				$order = wc_get_order( $order_id );

				if ( $order && is_object( $order ) ) {
					$data['payment_method'] = $order->get_payment_method_title();
				}
			}

			ob_start();
			el_get_template( 'pdf/invoice.php', array( 'data' => $data ) );
			$html = ob_get_contents();
			ob_get_clean();

			try {
				$mpdf = new \Mpdf\Mpdf( apply_filters( 'el_config_mpdf_invoice', $config_mpdf ) );
				$mpdf->WriteHTML( $html );
				$attach_file = WP_CONTENT_DIR . '/uploads/invoices/' . $pdf_name . '.pdf';
				$mpdf->Output( $attach_file, 'F' );
			} catch ( \Mpdf\MpdfException $e ) { // Note: safer fully qualified exception name used for catch
				// Process the exception, log, print etc.
				echo $e->getMessage();
			}

			return $attach_file;
		}

		function download_file_from_uploads($file_name): void {
			// Get the uploads directory and the full path to the file
			$upload_dir = wp_upload_dir();
			$file_path = $upload_dir['basedir'] . '/' . $file_name;

			// Check if the file exists
			if (file_exists($file_path)) {
				// Get the file's mime type
				$file_mime = mime_content_type($file_path);

				// Set the headers to force download
				header('Content-Description: File Transfer');
				header('Content-Type: ' . $file_mime);
				header('Content-Disposition: attachment; filename=' . basename($file_path));
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file_path));

				// Clear output buffer
				ob_clean();
				flush();

				// Read the file and output its contents
				readfile($file_path);
				exit;
			} else {
				wp_die('File not found.');
			}
		}
		function make_pdf_tickets( $ticket_ids ) {
			$ticket_list = array();
			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = array();

				$start_time    = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_start', true );
				$end_time      = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_end', true );
				$seat          = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'seat', true );
				$name_customer = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'name_customer', true );
				$desc_ticket   = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'desc_ticket', true );
				$venue         = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'venue', true );
				$logo_id       = get_post_meta( $ticket_id, OVA_METABOX_EVENT . "img", true );
				$person_type   = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'person_type', true );
				$extra_service = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'extra_service', true );

				// Get info ticket
				$ticket['ticket_id']  = $ticket_id;
				$ticket['event_name'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'name_event', true );

				if ( is_array( $venue ) ) {
					$ticket['venue'] = implode( ', ', $venue );
				}

				$ticket['address'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'address', true );

				$ticket['color_border_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'color_ticket', true );
				if ( $ticket['color_border_ticket'] == "#fff" || $ticket['color_border_ticket'] == "#ffffff" || empty( $ticket['color_border_ticket'] ) ) {
					$ticket['color_border_ticket'] = '#cccccc';
				}

				$ticket['color_label_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'color_label_ticket', true );
				if ( $ticket['color_label_ticket'] == "#fff" || $ticket['color_label_ticket'] == "#ffffff" || empty( $ticket['color_label_ticket'] ) ) {
					$ticket['color_label_ticket'] = '#666666';
				}

				$ticket['color_content_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'color_content_ticket', true );
				if ( $ticket['color_content_ticket'] == "#fff" || $ticket['color_content_ticket'] == "#ffffff" || empty( $ticket['color_content_ticket'] ) ) {
					$ticket['color_content_ticket'] = '#333333';
				}


				$ticket['private_desc_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'private_desc_ticket', true );
				// price ticket
				$ticket['price_ticket'] = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'price_ticket', true );
				//sub string
				$ticket['desc_ticket'] = sub_string_word( $desc_ticket, apply_filters( 'el_desc_ticket_characters', 230 ) );

				$ticket['date'] = date_i18n( get_option( 'date_format' ), $start_time );
				$ticket['time'] = date_i18n( get_option( 'time_format' ), $start_time ) . ' - ' . date_i18n( get_option( 'time_format' ), $end_time );

				$ticket['qrcode_str']  = get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'qr_code', true );
				$ticket['type_ticket'] = $seat ? get_the_title( $ticket_id ) . ' - ' . $seat : get_the_title( $ticket_id );

				if ( $person_type ) {
					$ticket['type_ticket'] .= ' - ' . $person_type;
				}

				$ticket['order_info'] = esc_html__( 'Ordered by', 'eventlist' ) . ' ' . $name_customer;
				// Logo
				$ticket['logo_url'] = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
				// Badge URLs
				$ticket['badge_urls'] = [
					"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo3.png",
					"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo2.png",
					"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo4.png",
					"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo5.png",
					"https://dev.seniorbarman.com/wp-content/uploads/2024/07/logo1.png",
				];
				// Extra service
				$ticket['extra_service'] = el_extra_sv_ticket( $extra_service );
				array_push( $ticket_list, $ticket );
			}

			$upload_dir = wp_upload_dir();

			// Add Font
			$defaultConfig = ( new Mpdf\Config\ConfigVariables() )->getDefaults();
			$fontDirs      = $defaultConfig['fontDir'];

			$defaultFontConfig = ( new Mpdf\Config\FontVariables() )->getDefaults();
			$fontData          = $defaultFontConfig['fontdata'];


			$config_mpdf = array(
				'tempDir'           => $upload_dir['basedir'],
				'default_font_size' => apply_filters( 'el_pdf_font_size_' . apply_filters( 'wpml_current_language', null ), 12 ),
				'default_font'      => apply_filters( 'el_pdf_font_' . apply_filters( 'wpml_current_language', null ), 'DejaVuSans' ),
				'fontDir'           => array_merge( $fontDirs, array( get_stylesheet_directory() . '/font' ) ),
				'margin_left'       => 0,
				'margin_right'      => 0,
				'margin_top'        => 0,
			);

			$attach_file = '';

			ob_start();
			el_get_template( 'pdf/template_cell.php', array( 'tickets' => $ticket_list ) );
			$html = ob_get_contents();
			ob_get_clean();

			try {
				$mpdf = new \Mpdf\Mpdf( apply_filters( 'el_config_mpdf', $config_mpdf ) );
				$mpdf->WriteHTML( $html );
				$attach_file = WP_CONTENT_DIR . '/uploads/event__ticket' . time() . '.pdf';
				$mpdf->Output( $attach_file, 'F' );
				$this->download_file_from_uploads('event__ticket' . time() . '.pdf');
				$mpdf->Output( $attach_file, 'I' );
			} catch ( \Mpdf\MpdfException $e ) { // Note: safer fully qualified exception name used for catch
				// Process the exception, log, print etc.
				echo $e->getMessage();
			}

			return $attach_file;
		}
	}
}