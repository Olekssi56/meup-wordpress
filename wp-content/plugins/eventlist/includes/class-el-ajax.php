<?php defined( 'ABSPATH' ) || exit;

if( !class_exists( 'El_Ajax' ) ){
	class El_Ajax{

		/**
		 * @var bool
		 */
		protected static $_loaded = false;

		public function __construct(){

			if ( self::$_loaded ) {
				return;
			}
			
			if (!defined('DOING_AJAX') || !DOING_AJAX)
				return;

			$this->init();

			self::$_loaded = true;
		}

		public function init(){

			// Define All Ajax function
			$arr_ajax =  array(

				// Vendor Update Profile
				'el_update_profile',

				// Vendor Add Social
				'el_add_social',

				// Vendor Save Social
				'el_save_social',

				// Check Password
				'el_check_password',

				// Update Password
				'el_change_password',

				// User Upgrade to Vendor Role
				'el_update_role',

				'el_check_vendor_field_required',

				// Process Checkout
				'el_process_checkout',

				// Countdown Checkout
				'el_countdown_checkout',

				// Check User Login
				'el_check_user_login',

				// Check Login to view report
				'el_check_login_report',

				// Vendor update a post to pending status
				'el_pending_post',

				// Vendor update a post to publish status
				'el_publish_post',

				// Vendor Move a post to trash status
				'el_trash_post',

				// Vendor clone a post 
				'el_duplicate_post',

				// Vendor delete a post
				'el_delete_post',

				// Vendor Choose Buld Action
				'el_bulk_action',

				// Booking check discount
				'el_check_discount',

				// Vendor add/update gallery
				'add_image_gallery',
				'change_image_gallery',

				// Load Location
				'el_load_location',

				// Save an event
				'el_save_edit_event',

				// Vendor export Booking to CSV
				'el_export_csv',

				// Vendor export Ticket to CSV
				'export_csv_ticket',

				// Vendor add a package
				'el_add_package',

				// The client add a event to wishlist
				'el_add_wishlist',

				// The client remove a event to wishlist
				'el_remove_wishlist',

				// The vendor update bank
				'el_update_payout_method',

				// Load location in search
				'el_load_location_search',

				// Search Map Page
				'el_search_map',

				// Display Event by filters in Elementor
				'el_filter_elementor_grid',

				// Send mail to vendor
				'el_single_send_mail_vendor',

				// Send mail when the client report an event
				'el_single_send_mail_report',

				// Update Ticket Status
				'el_update_ticket_status',

				// The customer cancel a booking
				'el_cancel_booking',

				//  add withdraw
				'el_add_withdrawal',

				// load schdules

				'el_load_schedules',

				// load ticket rest

				'el_load_ticket_rest',

                // chose calendar in manage sale
				'el_choose_calendar',

                //load edit ticket calendar in manage sale
				'el_load_edit_ticket_calendar',

				//	update ticket max

				'el_update_ticket_max',

				// check date search ticket

				'el_check_date_search_ticket',

				// multiple customers ticket
				'el_multiple_customers_ticket',

				// Upload files
				'el_upload_files',

				// Geocoding API
				'el_geocode',

				// Event List Default
				'el_event_default',

				// Event List Online
				'el_event_online',

				// Event List By Time
				'el_event_by_time',

				// Event Recent
				'el_event_recent',

				// recapcha
				'el_verify_google_recapcha',

				'el_ticket_received_download',

				'el_fe_unlink_download_ticket',

				'el_ticket_list',

				'el_ticket_transfer',

				'el_payment_countdown',
			);

			foreach($arr_ajax as $val){
				add_action( 'wp_ajax_'.$val, array( $this, $val ) );
				add_action( 'wp_ajax_nopriv_'.$val, array( $this, $val ) );
			}
		}




		//el_load_schedules

		public static function el_load_schedules() {
		    	/**
				* Hook: el_single_event_schedules_time - 10
		        * @hooked:  el_single_event_schedules_time - 10
				*/
				do_action( 'el_single_event_schedules_time' );

				wp_die();

		}

		//el_load_ticket_rest


		public static function el_load_ticket_rest() {
			if ( ! isset( $_POST['data'] ) ) wp_die();

			$show_remaining_tickets = EL()->options->event->get('show_remaining_tickets', 'yes');

			if ( $show_remaining_tickets != 'yes' ) return;

			$post_data 				= $_POST['data'];
			$time_value 			= isset( $post_data['time_value'] ) ? sanitize_text_field( $post_data['time_value'] ) : '';
			$id 					= isset( $post_data['ide'] ) ? sanitize_text_field( $post_data['ide'] ) : '';
			$date_format 			= get_option('date_format');
			$schedules_time 		= get_post_meta( $id, OVA_METABOX_EVENT . 'schedules_time', true );
			$list_type_ticket 		= get_post_meta( $id, OVA_METABOX_EVENT . 'ticket', true );
			$calendar_recurrence 	= get_post_meta( $id, OVA_METABOX_EVENT . 'calendar_recurrence', true );
			$seat_option 			= get_post_meta( $id, OVA_METABOX_EVENT . 'seat_option', true );
			$recurrence_frequency 	= get_post_meta( $id, OVA_METABOX_EVENT . 'recurrence_frequency', true );
			$ts_start 				= get_post_meta( $id, OVA_METABOX_EVENT . 'ts_start', true );
			$ts_end  				= get_post_meta( $id, OVA_METABOX_EVENT . 'ts_end', true );

			// Time Slot
			$is_timeslot = false;

			if ( $recurrence_frequency === 'weekly' && ! empty( $ts_start ) && ! empty( $ts_end ) ) {
				$is_timeslot = true;
			}

			$ticket_rest = array();

			if ( $calendar_recurrence ) {
				foreach ( $calendar_recurrence as $key_rec => $value_rec ) {
					if ( $is_timeslot ) {
						foreach ( $ts_start as $ts_key => $ts_value ) {
							if ( ! empty( $ts_value ) && is_array( $ts_value ) ) {
								foreach ( $ts_value as $ts_key_time => $ts_time ) {
									if ( $value_rec['calendar_id'] == $time_value.$ts_key.$ts_key_time ) {
										$total_number_ticket_rest = 0;

										if ( $total_number_ticket_rest == 1 ) {
											$ticket_text = esc_html__( 'ticket', 'eventlist' );
										} else {
											$ticket_text = esc_html__( 'tickets', 'eventlist' );
										}

										if ( $show_remaining_tickets == 'yes' ) { 
											if ( $seat_option != 'map' ) {
												foreach ( $list_type_ticket as $ticket ) {
													$number_ticket_rest = EL_Booking::instance()->get_number_ticket_rest( $id, $time_value.$ts_key.$ts_key_time,  $ticket['ticket_id']);

													$total_number_ticket_rest += $number_ticket_rest;
												}
											} else {
												$total_number_ticket_rest = EL_Booking::instance()->get_number_ticket_map_rest( $id, $time_value.$ts_key.$ts_key_time );
											}

											$number_ticket_text = '<span class="calendar_ticket_rest">('.$total_number_ticket_rest.'&nbsp;<span>'.$ticket_text.'</span>)</span>';
										} else {
											$number_ticket_text = '';
										}

										$ticket_rest[] = [
											'ticket' => $number_ticket_text,
											'id_cal' => $time_value.$ts_key.$ts_key_time,
										];
									}
								}
							}
						}
					} else {
						if ( $schedules_time ) {
							foreach ( $schedules_time as $key => $value ) {
								$total_number_ticket_rest = 0;

								if ( $total_number_ticket_rest == 1 ) {
									$ticket_text = esc_html__( 'ticket', 'eventlist' );
								} else {
									$ticket_text = esc_html__( 'tickets', 'eventlist' );
								}

								if ( $show_remaining_tickets == 'yes' ) { 
									if ( $seat_option != 'map' ) {
										foreach ( $list_type_ticket as $ticket ) {
											$number_ticket_rest = EL_Booking::instance()->get_number_ticket_rest( $id, $time_value.$key,  $ticket['ticket_id']);

											$total_number_ticket_rest += $number_ticket_rest;
										}
									} else {
										$total_number_ticket_rest = EL_Booking::instance()->get_number_ticket_map_rest($id, $time_value.$key);
									}

									$number_ticket_text = '<span class="calendar_ticket_rest">('.$total_number_ticket_rest.'&nbsp;<span>'.$ticket_text.'</span>)</span>';
								} else {
									$number_ticket_text = '';
								}

								if ( $value_rec['calendar_id'] == $time_value.$key ) {
									$ticket_rest[] = [
										'ticket' => $number_ticket_text,
										'id_cal' => $time_value.$key,
									];
								}
							}
						}
					}
				}
			}

			echo json_encode( $ticket_rest );
			wp_die();
		}



		// Update Ticket Status
		public static function el_update_ticket_status() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];

			$qr_code = $post_data['qr_code'];
			$ticket_info = EL_Ticket::validate_qrcode( array( 'check_qrcode' => $qr_code ) );

			echo json_encode( $ticket_info );
			wp_die();

		}

		public static function el_update_profile(){
			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data 	= $_POST['data'];

			$user_id 	= wp_get_current_user()->ID;

			$admin_approve_vendor = OVALG_Settings::admin_approve_vendor();
			$vendor_status = get_user_meta( $user_id, 'vendor_status', true );

			if( !isset( $post_data['el_update_profile_nonce'] ) || !wp_verify_nonce( $post_data['el_update_profile_nonce'], 'el_update_profile_nonce' ) ) return ;

			foreach ( $post_data as $key => $value ) {

				if ( $key === 'user_url' ) {
					wp_update_user( array( 'ID' => $user_id, 'user_url' => sanitize_url( $value ) ) );
					continue;
				}

				if (! is_array( $value ) ) {
					update_user_meta( $user_id, $key, sanitize_text_field( $value ) );
				} else {
					update_user_meta( $user_id, $key, $value );
				}
			}

			if ( $admin_approve_vendor !== 'no' ) {
				delete_user_meta( $user_id, 'vendor_status', 'reject' );
			}

			return true;
			wp_die();

		}

		/* Add Social */
		public static function el_add_social() {

			if( !isset( $_POST['data'] ) ) wp_die();
			
			$post_data = $_POST['data'];
			$index = isset( $post_data['index'] ) ? sanitize_text_field( $post_data['index'] ) : '';

			?>
			<div class="social_item vendor_field">
				<input type="text" name="<?php echo esc_attr('user_profile_social['.$index.'][link]'); ?>" class="link_social" value="" placeholder="<?php echo esc_attr( 'https://' ); ?>" autocomplete="off" autocorrect="off" autocapitalize="none" />
				<select name="<?php echo esc_attr('user_profile_social['.$index.'][icon]'); ?>" class="icon_social">
					<?php foreach (el_get_social() as $key => $value) { ?>
						<option value="<?php echo esc_attr($key); ?>"><?php echo esc_html( $value ); ?></option>
					<?php } ?>
				</select>
				<button class="button remove_social">x</button>
			</div>
			<?php

			wp_die();
		}

		/* Save Social */
		public static function el_save_social() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];
			$user_id = wp_get_current_user()->ID;
			if( !isset( $post_data['el_update_social_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_update_social_nonce'] ), 'el_update_social_nonce' ) ) return ;

			$post_data_sanitize = array();

			foreach ($post_data as $key => $value) {
				if ( is_array($value) ) {
					foreach ($value as $k1 => $v1) {
						$post_data_sanitize[$key][$k1][0] = esc_url_raw( $post_data[$key][$k1][0] );
						$post_data_sanitize[$key][$k1][1] = sanitize_text_field( $post_data[$key][$k1][1] );
					}
				} else {
					$post_data_sanitize[$key] = sanitize_text_field( $post_data[$key] );
				}
			}

			if ( !isset( $post_data_sanitize['user_profile_social'] ) ) {
				$post_data_sanitize['user_profile_social'] = array();
			}

			foreach($post_data_sanitize as $key => $value) {
				update_user_meta( $user_id, $key, $value );
			}

			wp_die();
		}

		/* Check password */
		public static function el_check_password() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];
			$user_id = wp_get_current_user()->ID;
			$password_database = wp_get_current_user()->user_pass;
			
			$old_password = isset( $post_data['old_password'] ) ? sanitize_text_field( $post_data['old_password'] ) : '';

			if( wp_check_password( $old_password, $password_database, $user_id ) == true && $old_password != '' ) {
				echo ('true');
			} else {
				echo 'false';
			}
			wp_die();
		}

		/* Change password */
		public static function el_change_password() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];
			
			if( !isset( $post_data['el_update_password_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_update_password_nonce'] ), 'el_update_password_nonce' ) ) return ;
			
			$user_id = wp_get_current_user()->ID;
			$password_database = wp_get_current_user()->user_pass;

			// Don't change password some user for testing.
			if( in_array( $user_id, apply_filters( 'user_id_testing', array() ) ) ){
				return;
			}
			

			$old_password = isset( $post_data['old_password'] ) ? sanitize_text_field( $post_data['old_password'] ) : '';
			$new_password = isset( $post_data['new_password'] ) ? sanitize_text_field( $post_data['new_password'] ) : '';
			
			if( wp_check_password( $old_password, $password_database, $user_id ) ) {
				wp_set_password( $new_password, $user_id );

				$redirect_url = wp_login_url();
				$redirect_url = add_query_arg( 'password', 'changed', $redirect_url );
				echo $redirect_url;
			}
			wp_die();
		}

		/* Pending post */
		public static function el_pending_post() {

			$post_data = $_POST['data'];
			
			if( !isset( $_POST['data'] ) ) wp_die();

			if( !isset( $post_data['el_pending_post_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_pending_post_nonce'] ), 'el_pending_post_nonce' ) ) return ;
			
			$post_id = isset( $post_data['post_id'] ) ? sanitize_text_field( $post_data['post_id'] ) : '';

			if( !verify_current_user_post( $post_id ) || !el_can_edit_event() ) return false;

			$my_post = array(
				'ID'          => $post_id,
				'post_status' => 'pending',
			);
			wp_update_post( $my_post );

			return true;
		}

		/* Pending post */
		public static function el_trash_post() {

			$post_data = $_POST['data'];
			
			if( !isset( $_POST['data'] ) ) wp_die();

			if( !isset( $post_data['el_trash_post_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_trash_post_nonce'] ), 'el_trash_post_nonce' ) ) return ;

			$post_id = isset( $post_data['post_id'] ) ? sanitize_text_field( $post_data['post_id'] ) : '';

			if( !verify_current_user_post( $post_id ) || !el_can_edit_event() ) return false;

			wp_trash_post( $post_id );

			return true;
		}

		/* duplicate post */
		public static function el_duplicate_post() {


			$post_data = $_POST['data'];
			
			if( !isset( $_POST['data'] ) ) wp_die();

			if( !isset( $post_data['el_duplicate_post_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_duplicate_post_nonce'] ), 'el_duplicate_post_nonce' ) ) return ;

			$post_id = isset( $post_data['post_id'] ) ? sanitize_text_field( $post_data['post_id'] ) : '';

			$check_create_event = el_check_create_event();

			$check =  $check_create_event['status'];

			$publish = EL()->options->role->get('publish_event', '1') ;

			if($publish =='1'){

				$publish = "publish";
			}else{

				$publish = "pending";
			}
			if( !verify_current_user_post( $post_id )) return false;

			$member_account_id = EL()->options->general->get( 'myaccount_page_id', '' );
			$redirect_page = get_the_permalink( $member_account_id );
			$redirect_page = add_query_arg( 'vendor', 'package', $redirect_page );		

			if( $check == 'false_total_event') {

				echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'Please register a package or upgrade to high package because your current package is limit number events. Click OK to setup package.', 'eventlist' ),  'url' => $redirect_page ) );
				wp_die();


			} else if($check == 'error'){

				echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'You don\'t have permission add new event. Click OK to setup package.', 'eventlist' ),  'url' => $redirect_page ) );
				wp_die();

			} else if($check == 'false_time_membership'){

				echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'Your package time is expired. Click OK to setup package.', 'eventlist' ),  'url' => $redirect_page ) );
				wp_die();				

			}else{


				global $wpdb;
				$post = get_post( $post_id );
				$current_user = wp_get_current_user();
				$new_post_author = $current_user->ID;




				$args = array(
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name,
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => $publish,
					'post_title'     => $post->post_title,
					'post_type'      => $post->post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order
				);


				$new_post_id = wp_insert_post( $args );


				$taxonomies = get_object_taxonomies($post->post_type); 
				foreach ($taxonomies as $taxonomy) {
					$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
					wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
				}


				$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
				if (count($post_meta_infos)!=0) {
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ($post_meta_infos as $meta_info) {
						$meta_key = $meta_info->meta_key;
						if( $meta_key == '_wp_old_slug' ) continue;
						$meta_value = addslashes($meta_info->meta_value);
						$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}
					$sql_query.= implode(" UNION ALL ", $sql_query_sel);
					$wpdb->query($sql_query);
				}
				
				$href = add_query_arg( array( 'vendor' => 'listing-edit', 'id' => $new_post_id  ), get_myaccount_page() );
				echo json_encode( array( 'href' => $href ));
				wp_die();
				wp_reset_postdata();
				wp_reset_query();
				exit;
				

			}
			return true;
		}

		/* Pending post */
		public static function el_delete_post() {

			$post_data = $_POST['data'];
			
			if( !isset( $_POST['data'] ) ) wp_die();

			if( !isset( $post_data['el_delete_post_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_delete_post_nonce'] ), 'el_delete_post_nonce' ) ) return ;

			$post_id = isset( $post_data['post_id'] ) ? sanitize_text_field( $post_data['post_id'] ) : '';

			if( !verify_current_user_post( $post_id ) || !el_can_delete_event() ) return false;

			wp_delete_post( $post_id, false );

			return true;
		}

		/* Publish post */
		public static function el_publish_post() {

			$post_data = $_POST['data'];
			$_prefix = OVA_METABOX_EVENT;
			
			if( !isset( $_POST['data'] ) ) wp_die();

			if( !isset( $post_data['el_publish_post_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_publish_post_nonce'] ), 'el_publish_post_nonce' ) ) return ;
			
			$post_id = isset( $post_data['post_id'] ) ? sanitize_text_field( $post_data['post_id'] ) : '';
			

			if( !verify_current_user_post( $post_id ) ) return false;

			if ( el_can_publish_event() ) {

				$my_post = array(
					'ID'          => $post_id,
					'post_status' => 'publish',
				);
				wp_update_post( $my_post );

			} else {

				$event_active = get_post_meta( $post_id, $_prefix.'event_active', true );

				switch ( $event_active ) {
					case '1': 
					$my_post = array(
						'ID'          => $post_id,
						'post_status' => 'publish',
					);
					wp_update_post( $my_post );
					break;

					default:
					$my_post = array(
						'ID'          => $post_id,
						'post_status' => 'pending',
					);
					wp_update_post( $my_post );
					break;
				}
			}
			return true;
		}

		/* Delete post */
		public static function el_bulk_action() {

			$post_data = $_POST['data'];
			$_prefix = OVA_METABOX_EVENT;
			
			if( !isset( $_POST['data'] ) ) wp_die();
			
			if( !isset( $post_data['el_bulk_action_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_bulk_action_nonce'] ), 'el_bulk_action_nonce' ) ) return ;

			$post_id = array();
			foreach ($post_data['post_id'] as $key => $value) {
				$post_id[$key] = sanitize_text_field( $post_data['post_id'][$key] );
			}

			$value_select = isset( $post_data['value_select'] ) ? sanitize_text_field( $post_data['value_select'] ) : '';
			
			foreach ($post_id as $key => $value) {

				if( !verify_current_user_post( $value ) ) return false;

				if ( ( $value_select == 'pending' || $value_select == 'restore' ) && el_can_edit_event() ) {
					$my_post = array(
						'ID'          => $value,
						'post_status' => 'pending',
					);
					wp_update_post( $my_post );

				} elseif( $value_select == 'trash' && el_can_edit_event() ) {
					$my_post = array(
						'ID'          => $value,
						'post_status' => 'trash',
					);
					wp_update_post( $my_post );

				} elseif( $value_select == 'publish' ) {

					if ( el_can_publish_event() ) {

						$my_post = array(
							'ID'          => $value,
							'post_status' => 'publish',
						);
						wp_update_post( $my_post );

					} else {
						
						$event_active = get_post_meta( $post_id, $_prefix.'event_active', true );

						switch ( $event_active ) {
							case '1': 
							$my_post = array(
								'ID'          => $value,
								'post_status' => 'publish',
							);
							wp_update_post( $my_post );
							break;

							default:
							$my_post = array(
								'ID'          => $value,
								'post_status' => 'pending',
							);
							wp_update_post( $my_post );
							break;
						}
					}

				} elseif( $value_select == 'delete' && el_can_delete_event() ) {
					wp_delete_post( $value );
				}
			}
			return true;
		}

		/* Add image gallery */
		public static function add_image_gallery() {

			if( !isset( $_POST['data'] ) ) wp_die();
			
			$post_data = $_POST['data'];
			$attachment = $post_data['attachment'];
			$index = isset( $post_data['index'] ) ? sanitize_text_field( $post_data['index'] ) : '';
			$_prefix = OVA_METABOX_EVENT;
			
			$el_thumbnail_path =  isset($attachment['sizes']['el_thumbnail']['url']) && $attachment['sizes']['el_thumbnail']['url'] ? $attachment['sizes']['el_thumbnail']['url'] : '';
			?>
			<div class="gallery_item">
				<input type="hidden" class="gallery_id" name="<?php echo esc_attr( $_prefix.'gallery['.$index.']' ); ?>" value="<?php echo esc_attr($attachment['id']); ?>">
				<?php if( $el_thumbnail_path ){ ?>
					<img class="image-preview" src="<?php echo esc_url($attachment['sizes']['el_thumbnail']['url']); ?>">
				<?php } ?>
				<a class="change_image_gallery button" href="#" data-uploader-title="<?php esc_attr_e( "Change image", 'eventlist' ); ?>" data-uploader-button-text="<?php esc_attr_e( "Change image", 'eventlist' ); ?>"><i class="fas fa-edit"></i></a>
				<a class="remove_image" href="#"><i class="far fa-trash-alt"></i></a>
			</div>
			<?php

			wp_die();
		}

		/* Change Image Gallery */
		public static function change_image_gallery() {

			if( !isset( $_POST['data'] ) ) wp_die();
			
			$post_data = $_POST['data'];
			$attachment = $post_data['attachment'];
			$index = isset( $post_data['index'] ) ? sanitize_text_field( $post_data['index'] ) : '';
			$_prefix = OVA_METABOX_EVENT;

			$el_thumbnail_path =  isset($attachment['sizes']['el_thumbnail']['url']) && $attachment['sizes']['el_thumbnail']['url'] ? $attachment['sizes']['el_thumbnail']['url'] : '';
			
			?>
			<input type="hidden" class="gallery_id" name="<?php echo esc_attr( $_prefix.'gallery['.$index.']' ); ?>" value="<?php echo esc_attr($attachment['id']); ?>">
			<?php if( $el_thumbnail_path ){ ?>
				<img class="image-preview" src="<?php echo esc_url($attachment['sizes']['el_thumbnail']['url']); ?>">
			<?php } ?>
			<a class="change_image_gallery button" href="#" data-uploader-title="<?php esc_attr_e( "Change image", 'eventlist' ); ?>" data-uploader-button-text="<?php esc_attr_e( "Change image", 'eventlist' ); ?>">
				<i class="fas fa-edit"></i></a>
			<a class="remove_image" href="#"><i class="far fa-trash-alt"></i></a>
			<?php

			wp_die();
		}

		/* Load location */
		public static function el_load_location() {
			
			if( !isset( $_POST['data'] ) ) wp_die();
			
			$post_data = $_POST['data'];
			$country = isset( $post_data['country'] ) ? sanitize_text_field( $post_data['country'] ) : '';
			$city_selected = isset( $post_data['city_selected'] ) ? sanitize_text_field( $post_data['city_selected'] ) : '';

			if ($country != '') {

				$country = get_term_by( 'slug', $country, 'event_loc' );
				
				$get_city = get_terms( 'event_loc', array( 'parent' => $country->term_id, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false ) );
				
				?>	
				<option value=""><?php esc_html_e( 'All Cities', 'eventlist' ); ?></option> 
				<?php

				foreach ($get_city as $v_city) {
					$v_city_slug = isset( $v_city->slug ) ? apply_filters( 'editable_slug', $v_city->slug, $v_city ) : '';
				?>

					<option value="<?php echo esc_attr($v_city_slug); ?>" <?php echo esc_attr( $city_selected == $v_city_slug ? 'selected' : '' ); ?> ><?php echo esc_html($v_city->name); ?></option>

				<?php }

			} else {

				$parent_terms = get_terms( 'event_loc', array( 'parent' => 0, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false ) ); 
				?>	
				<option value=""><?php esc_html_e( 'All Cities', 'eventlist' ); ?></option> 
				<?php

				foreach ( $parent_terms as $pterm ) {

					$terms = get_terms( 'event_loc', array( 'parent' => $pterm->term_id, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false ) );
					?>

					<?php
					foreach ( $terms as $term ) { 
						$term_slug = isset( $term->slug ) ? apply_filters( 'editable_slug', $term->slug, $term ) : '';
					?>
						<option value="<?php echo esc_attr($term_slug); ?>" <?php echo esc_attr( $city_selected == $term_slug ? 'selected' : '' ); ?> ><?php echo esc_html($term->name); ?></option>

					<?php	}
				}
			}

			wp_die();
		}

		/* Save Edit Event */
		public static function el_save_edit_event() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];

			$_prefix = OVA_METABOX_EVENT;

			if( !isset( $post_data['el_edit_event_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_edit_event_nonce'] ), 'el_edit_event_nonce' ) ) return ;

			$current_user = get_current_user_id();

			$post_data_sanitize = array();
			foreach ($post_data as $key => $value) {

				if (!is_array($value)) {
					$post_data_sanitize[$_prefix.$key] = sanitize_text_field( $post_data[$key] );
				} else {
					foreach ($post_data[$key] as $k1 => $v1) {
						if (!is_array($v1)) {
							$post_data_sanitize[$_prefix.$key][$k1] = sanitize_text_field( $post_data[$key][$k1] );
						} else {
							foreach ($v1 as $k2 => $v2) {
								if (!is_array($v2)) {
									$post_data_sanitize[$_prefix.$key][$k1][$k2] = sanitize_text_field( $post_data[$key][$k1][$k2] );
								} else {
									foreach ($v2 as $k3 => $v3) {
										if (!is_array($v3)) {
											$post_data_sanitize[$_prefix.$key][$k1][$k2][$k3] = sanitize_text_field( $post_data[$key][$k1][$k2][$k3] );
										} else {
											foreach ($v3 as $k4 => $v4) {
												if (!is_array($v4)) {
													$post_data_sanitize[$_prefix.$key][$k1][$k2][$k3][$k4] = sanitize_text_field( $post_data[$key][$k1][$k2][$k3][$k4] );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$content_event = isset( $post_data['content_event'] ) ? wp_kses_post( $post_data['content_event'] ) : '';

			$post_id = isset( $post_data_sanitize[$_prefix.'post_id'] ) ? $post_data_sanitize[$_prefix.'post_id'] : '';
			$author_id = get_post_field( 'post_author', $post_id ) ? get_post_field( 'post_author', $post_id ) : '';

			$name_event = isset( $post_data_sanitize[$_prefix.'name_event'] ) ?  $post_data_sanitize[$_prefix.'name_event']  : '';
			
			$event_cat = isset( $post_data_sanitize[$_prefix.'event_cat'] ) ? $post_data_sanitize[$_prefix.'event_cat'] : '';

			$time_zone = isset( $post_data_sanitize[$_prefix.'time_zone'] ) ? $post_data_sanitize[$_prefix.'time_zone'] : '';

			$data_taxonomy = isset( $post_data_sanitize[$_prefix.'data_taxonomy'] ) ? $post_data_sanitize[$_prefix.'data_taxonomy'] : [];


			
			$check_allow_change_tax = check_allow_change_tax_by_event($post_id);
			$check_allow_change_tax_user = check_allow_change_tax_by_user_login();
			$enable_tax = EL()->options->tax_fee->get('enable_tax');

			
			
			$event_tag = isset( $post_data_sanitize[$_prefix.'event_tag'] ) ? $post_data_sanitize[$_prefix.'event_tag'] : array();

			$event_state = isset( $post_data_sanitize[$_prefix.'event_state'] ) ? $post_data_sanitize[$_prefix.'event_state'] : '';
			$event_city = isset( $post_data_sanitize[$_prefix.'event_city'] ) ? $post_data_sanitize[$_prefix.'event_city'] : '';
			
			$img_thumbnail = isset( $post_data_sanitize[$_prefix.'img_thumbnail'] ) ? sanitize_text_field( $post_data_sanitize[$_prefix.'img_thumbnail'] ) : '';

			
			if( isset( $post_data_sanitize[$_prefix.'venue'] ) && $post_data_sanitize[$_prefix.'venue'] ){
				foreach ( $post_data_sanitize[$_prefix.'venue'] as $value ) {

					$value = isset( $value ) ? sanitize_text_field( $value ) : '';

					if (!el_get_page_by_title( $value, OBJECT, 'venue' )) {
						$venue_info = array(
							'post_author' => $current_user,
							'post_title' => sanitize_text_field( $value ),
							'post_content' => '',
							'post_type' => 'venue',
							'post_status' => 'publish',
							'_thumbnail_id' => '',
						);

						wp_insert_post( $venue_info, true ); 
					}
				}
			}

			/* Check image thumbnail exits */
			if (!$img_thumbnail) {
				delete_post_thumbnail($post_id);
			}


			/* Check event_tax exits */
			if ( ( isset( $post_data_sanitize[$_prefix.'event_tax'] ) && !$post_data_sanitize[$_prefix.'event_tax'] ) || $check_allow_change_tax_user != 'yes' || $enable_tax != 'yes' ) {
				$post_data_sanitize[$_prefix.'event_tax'] = 0;
			}

			/* Check event_type exits */
			if ( ( isset( $post_data_sanitize[$_prefix.'event_type'] ) && !$post_data_sanitize[$_prefix.'event_type'] ) ) {
				$post_data_sanitize[$_prefix.'event_type'] = 'classic';
			}

			if ( ( isset( $post_data_sanitize[$_prefix.'ticket_link'] ) && !$post_data_sanitize[$_prefix.'ticket_link'] ) ) {
				$post_data_sanitize[$_prefix.'ticket_link'] = 'ticket_internal_link';
			}

			if ( ( isset( $post_data_sanitize[$_prefix.'ticket_external_link'] ) && !$post_data_sanitize[$_prefix.'ticket_external_link'] ) ) {
				$post_data_sanitize[$_prefix.'ticket_external_link'] = '';
			}

			if ( ( isset( $post_data_sanitize[$_prefix.'ticket_external_link_price'] ) && !$post_data_sanitize[$_prefix.'ticket_external_link_price'] ) ) {
				$post_data_sanitize[$_prefix.'ticket_external_link_price'] = '';
			}

			/* Check social exits */
			if ( !isset( $post_data_sanitize[$_prefix.'social_organizer'] ) || !$post_data_sanitize[$_prefix.'social_organizer'] ) {
				$post_data_sanitize[$_prefix.'social_organizer'] = array();
			}

			/* Check image gallery exits */
			if ( !isset( $post_data_sanitize[$_prefix.'gallery'] ) || !$post_data_sanitize[$_prefix.'gallery'] ) {
				$post_data_sanitize[$_prefix.'gallery'] = array();
			}

			/* Check image banner exits */
			if ( !isset( $post_data_sanitize[$_prefix.'image_banner'] ) || !$post_data_sanitize[$_prefix.'image_banner'] ) {
				$post_data_sanitize[$_prefix.'image_banner'] = '';
			}		

			/* Check Ticket exits */
			if( !isset( $post_data_sanitize[$_prefix.'ticket'] ) || !$post_data_sanitize[$_prefix.'ticket'] ){
				$post_data_sanitize[$_prefix.'ticket'] = array();
			}

			/* Check calendar exits */
			if ( !isset( $post_data_sanitize[$_prefix.'calendar'] ) || !$post_data_sanitize[$_prefix.'calendar'] ) {
				$post_data_sanitize[$_prefix.'calendar'] = array();
			}

			/* Check schedules_time exits */
			if ( !isset( $post_data_sanitize[$_prefix.'schedules_time'] ) || !$post_data_sanitize[$_prefix.'schedules_time'] ) {
				$post_data_sanitize[$_prefix.'schedules_time'] = array();
			}


			/* Check Disable Date exits */
			if ( !isset( $post_data_sanitize[$_prefix.'disable_date'] ) || !$post_data_sanitize[$_prefix.'disable_date'] ) {
				$post_data_sanitize[$_prefix.'disable_date'] = array();
			}

			/* Check Disable Time Slot exits */
			if ( !isset( $post_data_sanitize[$_prefix.'disable_date_time_slot'] ) || !$post_data_sanitize[$_prefix.'disable_date_time_slot'] ) {
				$post_data_sanitize[$_prefix.'disable_date_time_slot'] = array();
			}

			/* Check coupon exits */
			if ( !isset( $post_data_sanitize[$_prefix.'coupon'] ) || !$post_data_sanitize[$_prefix.'coupon'] ) {
				$post_data_sanitize[$_prefix.'coupon'] = array();
			}


			/* Check Venue exits */
			if( !isset( $post_data_sanitize[$_prefix.'venue'] ) || !$post_data_sanitize[$_prefix.'venue'] ){
				$post_data_sanitize[$_prefix.'venue'] = array();
			}

			/* Check recurrence bydays exits */
			if( !isset( $post_data_sanitize[$_prefix.'recurrence_bydays'] ) || !$post_data_sanitize[$_prefix.'recurrence_bydays'] ){
				$post_data_sanitize[$_prefix.'recurrence_bydays'] = array();
			}

			/* Check recurrence interval exits */
			if( !isset( $post_data_sanitize[$_prefix.'recurrence_interval'] ) || !$post_data_sanitize[$_prefix.'recurrence_interval'] ){
				$post_data_sanitize[$_prefix.'recurrence_interval'] = '1';
			}

			$ticket_prices 	= array();
			$seat_option 	= isset( $post_data_sanitize[$_prefix.'seat_option'] ) ? $post_data_sanitize[$_prefix.'seat_option'] : '';

			$k = 0;
			$decimal_separator 	= EL()->options->general->get('decimal_separator','.');

			if( isset( $post_data_sanitize[$_prefix.'ticket'] ) && $post_data_sanitize[$_prefix.'ticket'] ){
				foreach ($post_data_sanitize[$_prefix.'ticket'] as $key => $value) {
					if ($value['ticket_id'] == '') {
						$post_data_sanitize[$_prefix.'ticket'][$key]['ticket_id'] = FLOOR(microtime(true)) + $k;
						$k++;
					}

					if ($value['setup_seat'] == '') {

						$post_data_sanitize[$_prefix.'ticket'][$key]['setup_seat'] =  'yes';

					}

					if ( $value['price_ticket'] ) {
						$price = $value['price_ticket'];
						$new_price = str_replace( $decimal_separator, ".", $price );
						if ( $price !== $new_price ) {
							$post_data_sanitize[$_prefix.'ticket'][$key]['price_ticket'] = $new_price;
						}
						$ticket_prices['none'][] = (float) $new_price;
						$ticket_prices['simple'][] = (float) $new_price;
					}
				}
			}

			$ticket_link = isset( $post_data_sanitize[$_prefix.'ticket_link'] ) ? $post_data_sanitize[$_prefix.'ticket_link'] : '';

			if ( $ticket_link !== 'ticket_internal_link' ) {
				if ( isset( $post_data_sanitize[$_prefix.'ticket_external_link_price'] )  ) {
					$price = $post_data_sanitize[$_prefix.'ticket_external_link_price'] ? (float) $post_data_sanitize[$_prefix.'ticket_external_link_price'] : '';
					if ( $price ) {
						$ticket_prices['ticket_external_link'][] = $price;
					}
				}
			}

			if( isset( $post_data_sanitize[$_prefix.'calendar'] ) && $post_data_sanitize[$_prefix.'calendar'] ){
				foreach ($post_data_sanitize[$_prefix.'calendar'] as $key => $value) {
					if ($value['calendar_id'] == '') {
						$post_data_sanitize[$_prefix.'calendar'][$key]['calendar_id'] = FLOOR(microtime(true)) + $k;
						$k++;
					}
					if ($value['date'] == '') {
						unset($post_data_sanitize[$_prefix.'calendar'][$key]);
					}
				}
			}

			if( isset( $post_data_sanitize[$_prefix.'coupon'] ) && $post_data_sanitize[$_prefix.'coupon'] ){
				foreach ($post_data_sanitize[$_prefix.'coupon'] as $key => $value) {
					if ($value['coupon_id'] == '') {
						$post_data_sanitize[$_prefix.'coupon'][$key]['coupon_id'] = FLOOR(microtime(true)) + $k;
						$k++;
					}
				}
			}

			/* Check checbox info organizer exits */
			if( !isset( $post_data_sanitize[$_prefix.'info_organizer'] ) || !$post_data_sanitize[$_prefix.'info_organizer'] ){
				$post_data_sanitize[$_prefix.'info_organizer'] = '';
			}else{
				$post_data_sanitize[$_prefix.'info_organizer'] = 'checked';
			}

			/* Check checbox info organizer exits */
			if( !isset( $post_data_sanitize[$_prefix.'edit_full_address'] ) || !$post_data_sanitize[$_prefix.'edit_full_address'] ){
				$post_data_sanitize[$_prefix.'edit_full_address'] = '';
			}else{
				$post_data_sanitize[$_prefix.'edit_full_address'] = 'checked';
			}

			// Time Slot
			$recurrence_time_slot = array();

			/* Check Calendar Auto */ 
			if ( isset( $post_data_sanitize[$_prefix.'option_calendar'] ) && $post_data_sanitize[$_prefix.'option_calendar'] == 'auto' ) {
				$recurrence_days = get_recurrence_days(
					$post_data_sanitize[$_prefix.'recurrence_frequency'], 
					$post_data_sanitize[$_prefix.'recurrence_interval'], 
					$post_data_sanitize[$_prefix.'recurrence_bydays'], 
					$post_data_sanitize[$_prefix.'recurrence_byweekno'], 
					$post_data_sanitize[$_prefix.'recurrence_byday'], 
					$post_data_sanitize[$_prefix.'calendar_start_date'], 
					$post_data_sanitize[$_prefix.'calendar_end_date'] 
				);

				$post_data_sanitize[$_prefix.'calendar_recurrence'] = array();

				$ts_start 	= [];
				$ts_end 	= [];

				if ( isset( $post_data_sanitize[$_prefix.'ts_start'] ) && $post_data_sanitize[$_prefix.'ts_start'] && is_array( $post_data_sanitize[$_prefix.'ts_start'] ) ) {
					foreach ( $post_data_sanitize[$_prefix.'ts_start'] as $item_ts_star ) {
						if ( ! empty( $item_ts_star ) && is_array( $item_ts_star ) ) {
							foreach ( $item_ts_star as $k => $item_times ) {
								if ( ! empty( $item_times ) && is_array( $item_times ) ) {
									$ts_start[$k] = $item_times;
								}
							}
						}
					}
				}

				if ( isset( $post_data_sanitize[$_prefix.'ts_end'] ) && $post_data_sanitize[$_prefix.'ts_end'] && is_array( $post_data_sanitize[$_prefix.'ts_end'] ) ) {
					foreach ( $post_data_sanitize[$_prefix.'ts_end'] as $item_ts_end ) {
						if ( ! empty( $item_ts_end ) && is_array( $item_ts_end ) ) {
							foreach ( $item_ts_end as $k => $item_times ) {
								if ( ! empty( $item_times ) && is_array( $item_times ) ) {
									$ts_end[$k] = $item_times;
								}
							}
						}
					}
				}
				
				$post_data_sanitize[$_prefix.'ts_start'] 	= $ts_start ? $ts_start : '';
				$post_data_sanitize[$_prefix.'ts_end'] 		= $ts_end ? $ts_end : '';

				foreach ( $recurrence_days as $key => $value ) {
					if ( isset( $post_data_sanitize[$_prefix.'schedules_time'] ) ) {
						foreach ($post_data_sanitize[$_prefix.'schedules_time'] as $key_schedule => $value_schedule) {
							$post_data_sanitize[$_prefix.'calendar_recurrence'][] = [
								'calendar_id' => $value.$key_schedule,
								'date' => date('Y-m-d', $value),
								'start_time' => $value_schedule['start_time'],
								'end_time' => $value_schedule['end_time'],
								'book_before' => $value_schedule['book_before'],
							];
						}
					}

					$post_data_sanitize[$_prefix.'calendar_recurrence'][] = [
						'calendar_id' 	=> $value,
						'date' 			=> date('Y-m-d', $value),
						'start_time' 	=> $post_data_sanitize[$_prefix.'calendar_recurrence_start_time'],
						'end_time' 		=> $post_data_sanitize[$_prefix.'calendar_recurrence_end_time'],
						'book_before' 	=> $post_data_sanitize[$_prefix.'calendar_recurrence_book_before'],
					];

					if ( $post_data_sanitize[$_prefix.'option_calendar'] == 'auto' && $post_data_sanitize[$_prefix.'recurrence_frequency'] == 'weekly' && isset( $post_data_sanitize[$_prefix.'recurrence_bydays'] ) && ! empty( $post_data_sanitize[$_prefix.'recurrence_bydays'] ) ) {

						$weekday = date( 'N', $value );

						if ( $weekday == 7 ) {
							$weekday = 0;
						}

						foreach ( $post_data_sanitize[$_prefix.'recurrence_bydays'] as $k_bydays => $v_bydays ) {
							if ( $weekday == $v_bydays && isset( $post_data_sanitize[$_prefix.'ts_start'][$v_bydays] ) && isset( $post_data_sanitize[$_prefix.'ts_end'][$v_bydays] ) && ! empty( $post_data_sanitize[$_prefix.'ts_start'][$v_bydays] ) && ! empty( $post_data_sanitize[$_prefix.'ts_end'][$v_bydays] ) ) {

								foreach ( $post_data_sanitize[$_prefix.'ts_start'][$v_bydays] as $k_ts_start => $v_ts_start ) {
									if ( isset( $post_data_sanitize[$_prefix.'ts_end'][$v_bydays][$k_ts_start] ) && $post_data_sanitize[$_prefix.'ts_end'][$v_bydays][$k_ts_start] ) {

										$recurrence_time_slot[] = [
											'calendar_id' 	=> $value.$v_bydays.$k_ts_start,
											'date' 			=> date('Y-m-d', $value),
											'start_time' 	=> $v_ts_start,
											'end_time' 		=> $post_data_sanitize[$_prefix.'ts_end'][$v_bydays][$k_ts_start],
											'book_before' 	=> apply_filters( 'el_tf_time_slot_book_before', 0, $post_id ),
										];
									}
								}
							}
						}
					}
				}

				if ( ! empty( $recurrence_time_slot ) && is_array( $recurrence_time_slot ) ) {
					$post_data_sanitize[$_prefix.'calendar_recurrence'] = $recurrence_time_slot;
				}
			}

			/* Disable Date */
			$arr_disable_date = array();
			$total_key_disable_date = 0;
			if ( isset( $post_data_sanitize[$_prefix.'disable_date'] ) && ! empty( $post_data_sanitize[$_prefix.'disable_date'] ) ) {
				foreach ($post_data_sanitize[$_prefix.'disable_date'] as $key => $value) {

					if ( $value['start_date'] == '' && $value['end_date'] != '' ) {
						$post_data_sanitize[$_prefix.'disable_date'][$key]['start_date'] =  $post_data_sanitize[$_prefix.'disable_date'][$key]['end_date'];
					}

					if ( $value['start_date'] != '' && $value['end_date'] == '' ) {
						$post_data_sanitize[$_prefix.'disable_date'][$key]['end_date'] =  $post_data_sanitize[$_prefix.'disable_date'][$key]['start_date'];
					}

					if ( $value['start_date'] == '' && $value['end_date'] == '' ) {
						unset( $post_data_sanitize[$_prefix.'disable_date'][$key] );
					}

					$total_key_disable_date = $key;
				}

				if( isset($total_key_disable_date) && $total_key_disable_date ){
					for ($i = 0; $i <= $total_key_disable_date; $i++) {

						$number_date = ( strtotime( $post_data_sanitize[$_prefix.'disable_date'][$i]['end_date'] ) - strtotime( $post_data_sanitize[$_prefix.'disable_date'][$i]['start_date'] ) ) / 86400;

						for ( $x = 0; $x <= $number_date; $x++ ) {
							$arr_disable_date []= [
								'date' => strtotime( ($x).' days' , strtotime( $post_data_sanitize[$_prefix.'disable_date'][$i]['start_date'] ) ),
								'time' =>  $post_data_sanitize[$_prefix.'disable_date'][$i]['schedules_time'],
							];
						}

					}
				}
			}

			/* Disable Time Slot */
			if ( isset( $post_data_sanitize[$_prefix.'disable_date_time_slot'] ) && ! empty( $post_data_sanitize[$_prefix.'disable_date_time_slot'] ) ) {
				foreach ( $post_data_sanitize[$_prefix.'disable_date_time_slot'] as $k => $ts_item ) {

					if ( $ts_item['start_date'] == '' && $ts_item['end_date'] != '' ) {
						$post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['start_date'] = $post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['end_date'];
					}

					if ( $ts_item['start_date'] != '' && $ts_item['end_date'] == '' ) {
						$post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['end_date'] = $post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['end_date'];
					}

					if ( $ts_item['start_time'] ) {
						$post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['start_time'] = $ts_item['start_time'];
					} else {
						$post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['start_time'] = '';
					}

					if ( $ts_item['end_time'] ) {
						$post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['end_time'] = $ts_item['end_time'];
					} else {
						$post_data_sanitize[$_prefix.'disable_date_time_slot'][$k]['end_time'] = '';
					}
				}
			}

			/* Remove date disabled */
			if ( isset( $post_data_sanitize[$_prefix.'calendar_recurrence'] ) && ! empty( $post_data_sanitize[$_prefix.'calendar_recurrence'] ) ) {
				if ( ! empty( $recurrence_time_slot ) && is_array( $recurrence_time_slot ) ) {
					if ( isset( $post_data_sanitize[$_prefix.'disable_date_time_slot'] ) && ! empty( $post_data_sanitize[$_prefix.'disable_date_time_slot'] ) ) {
						foreach ( $post_data_sanitize[$_prefix.'calendar_recurrence'] as $key => $value ) {
							foreach ( $post_data_sanitize[$_prefix.'disable_date_time_slot'] as $ts_item ) {
								$cal_start 	= strtotime( $value['date'] . ' ' . $value['start_time'] ) - absint( $value['book_before'] * 60 );
								$cal_end 	= strtotime( $value['date'] . ' ' . $value['end_time'] );

								$ts_start 	= strtotime( $ts_item['start_date'] . ' ' . $ts_item['start_time'] );
								$ts_end 	= strtotime( $ts_item['end_date'] . ' ' . $ts_item['end_time'] );

								if ( ! ( $ts_start >= $cal_end || $ts_end <= $cal_start ) ) {
									unset( $post_data_sanitize[$_prefix.'calendar_recurrence'][$key] );
								}
							}
						}
					}
				} else {
					if ( ! empty( $arr_disable_date ) && is_array( $arr_disable_date ) ) {
						foreach ( $post_data_sanitize[$_prefix.'calendar_recurrence'] as $key => $value ) {
							foreach ( $arr_disable_date as $v_date) {
								if ( $v_date['date'].$v_date['time'] == $value['calendar_id'] ) {
									unset($post_data_sanitize[$_prefix.'calendar_recurrence'][$key]);
								}
							}
						}
					}
				}
			}
			
			/* Date strtotime */
			$arr_start_date = array();
			$event_days = '';
			$arr_end_date = array();
			if ($post_data_sanitize[$_prefix.'option_calendar'] == 'manual') {
				if ( isset( $post_data_sanitize[$_prefix.'calendar'] ) ) {
					foreach ($post_data_sanitize[$_prefix.'calendar'] as $value) {
						$arr_start_date[] = strtotime( $value['date'] .' '. $value['start_time'] );
						$arr_end_date[] = strtotime( $value['end_date'] .' '. $value['end_time'] );
						$all_date_betweens_day = el_getDatesFromRange( date( 'Y-m-d', strtotime( $value['date'] ) ), date( 'Y-m-d', strtotime( $value['end_date'] )+24*60*60 ) );
						foreach ($all_date_betweens_day as $v) {
							$event_days .= $v.'-';
						}
					}
				}
			} else {
				if ( isset( $post_data_sanitize[$_prefix.'calendar_recurrence'] ) ) {
					foreach ($post_data_sanitize[$_prefix.'calendar_recurrence'] as $value) {
						$arr_start_date[] = strtotime( $value['date'] .' '. $value['start_time'] );
						$arr_end_date[] = strtotime( $value['date'] .' '. $value['end_time'] );
						$event_days .= strtotime( $value['date'] ).'-';
					}
				}
			}

			// store all days of event
			$post_data_sanitize[$_prefix.'event_days'] = $event_days;

			if ( $arr_start_date != array() )  {
				$post_data_sanitize[$_prefix.'start_date_str'] = min($arr_start_date);
			} else {
				$post_data_sanitize[$_prefix.'start_date_str'] = '';
			}
			

			if ( $arr_end_date != array() ) {
				$post_data_sanitize[$_prefix.'end_date_str'] = max($arr_end_date);
			} else {
				$post_data_sanitize[$_prefix.'end_date_str'] = '';
			}

			// Extra Service
			if ( isset( $post_data_sanitize[$_prefix.'extra_service'] ) ) {
				$extra_service = $post_data_sanitize[$_prefix.'extra_service'];
				if ( ! empty( $extra_service ) ) {
					foreach ( $extra_service as $k => $val ) {
						$id = isset( $val['id'] ) && ! empty( $val['id'] ) ? $val['id'] : uniqid();
						$extra_service[$k]['id'] = $id;
					}
					$post_data_sanitize[$_prefix.'extra_service'] = $extra_service;
				} else {
					$post_data_sanitize[$_prefix.'extra_service'] = [];
				}
			}


			/* Remove empty field seat map */
			if( isset( $post_data_sanitize[$_prefix.'ticket_map']['seat'] ) && $post_data_sanitize[$_prefix.'ticket_map']['seat'] ){
				foreach ($post_data_sanitize[$_prefix.'ticket_map']['seat'] as $key => $value) {
					if ( $value['id'] == '' || $value['price'] == '' ) {
						unset($post_data_sanitize[$_prefix.'ticket_map']['seat'][$key]);
					} else {
						$ticket_prices['map'][] = (float) $value['price'];
					}
				}
			}

			/* Remove empty field area map */
			if ( isset( $post_data_sanitize[$_prefix.'ticket_map']['area'] ) && $post_data_sanitize[$_prefix.'ticket_map']['area'] ) {
				foreach ( $post_data_sanitize[$_prefix.'ticket_map']['area'] as $key => $value ) {

					$flag = false;
					$ticket_area_price = '';

					if ( $value['person_price'] ) {
						$person_price = stripslashes( $value['person_price'] );
						$person_price = json_decode( $person_price , true );
						foreach ( $person_price as $_key => $_value ) {
							if ( $_value == '' || (float) $_value <= 0 ) {
								$flag = true;
							} else {
								$ticket_area_price = (float) $_value;
							}
						}
					} else if ( $value['price'] == '' ) {
						$flag = true;
					} else {
						$ticket_area_price = (float) $value['price'];
					}

					if ( $value['qty'] == '' ) {
						$flag = true;
					}

					if ( $value['id'] == '' ) {
						$flag = true;
					}
					
					if ( $flag ) {
						unset( $post_data_sanitize[$_prefix.'ticket_map']['area'][$key] );
					} else {
						if ( $ticket_area_price ) {
							$ticket_prices['map'][] = $ticket_area_price;
						}
					}
				}
			}

			/* Remove empty field description seat map */
			if( isset( $post_data_sanitize[$_prefix.'ticket_map']['desc_seat'] ) && $post_data_sanitize[$_prefix.'ticket_map']['desc_seat'] ){
				foreach ($post_data_sanitize[$_prefix.'ticket_map']['desc_seat'] as $key => $value) {
					if ( $value['map_price_type_seat'] == '' || $value['map_type_seat'] == '' ) {
						unset($post_data_sanitize[$_prefix.'ticket_map']['desc_seat'][$key]);
					}
				}
			}

			// min_max_price
			$min_max_price = '';
			if ( count( $ticket_prices ) > 0 ) {
				if ( $ticket_link === 'ticket_external_link' ) {
					if ( isset( $ticket_prices['ticket_external_link'] ) ) {
						$min_max_price = implode("-", $ticket_prices['ticket_external_link']);
					} else {
						$min_max_price = '0';
					}
				} else {
					switch ( $seat_option ) {
						case 'none':
							if ( isset( $ticket_prices['none'] ) ) {
								$min_max_price = implode("-", $ticket_prices['none']);
							} else {
								$min_max_price = '0';
							}
							break;
						case 'simple':
							if ( isset( $ticket_prices['simple'] ) ) {
								$min_max_price = implode("-", $ticket_prices['simple']);
							} else {
								$min_max_price = '0';
							}
							break;
						case 'map':
							if ( isset( $ticket_prices['map'] ) ) {
								$min_max_price = implode("-", $ticket_prices['map']);
							} else {
								$min_max_price = '0';
							}
							break;
						default:
							break;
					}
				}
			} else {
				$min_max_price = '0';
			}

			$min_price = '0';
			$max_price = '0';

			if ( $min_max_price != '' ) {
				$min_max_price = explode("-", $min_max_price);
				$min_max_price = array_map('floatval', $min_max_price);
				$min_price = min($min_max_price);
				$max_price = max($min_max_price);
			}

			$post_data_sanitize[$_prefix.'min_price'] = $min_price;
			$post_data_sanitize[$_prefix.'max_price'] = $max_price;

			/* Save Edit Post */
			if ( $post_id != '' ) {

				if( !el_can_edit_event() ) {echo 'error'; wp_die();}

				/* Location */
				$event_loc = array();
				if( $event_state && $event_state_obj = get_term_by('slug', $event_state, 'event_loc') ){
					$event_loc[] = $event_state_obj->term_id ? $event_state_obj->term_id : '';
				}

				if( $event_city && $event_city_obj = get_term_by('slug', $event_city, 'event_loc') ){
					$event_loc[] = $event_city_obj->term_id ? $event_city_obj->term_id : '';
				}
				

				if( !empty( $event_loc ) ){
					wp_set_post_terms( $post_id, array_filter( $event_loc ) , 'event_loc' );	
				}
				

				/* Cat */
				if( !empty( $event_cat ) ){
					wp_set_post_terms( $post_id, $event_cat , 'event_cat' );
				}


				/* Custom Taxonomy */
				if( ! empty( $data_taxonomy ) ){
					foreach( $data_taxonomy as $slug_taxonomy => $val_taxonomy ) {
						wp_set_post_terms( $post_id, $val_taxonomy , $slug_taxonomy );
					}
				}


				/* Tags */
				if( !empty( $event_tag ) ){
					wp_set_post_terms( $post_id, $event_tag , 'event_tag' );
				}

				/* Check event_tax exits */
				if (  ( isset( $post_data_sanitize[$_prefix.'event_tax'] ) && ! $post_data_sanitize[$_prefix.'event_tax'] ) || $check_allow_change_tax != 'yes' || $enable_tax != 'yes' ) {
					$post_data_sanitize[$_prefix.'event_tax'] = 0;
				}

				/* Update Pay Status */
				$post_data_sanitize[$_prefix.'status_pay'] = get_post_meta( $post_id, $_prefix.'status_pay', true ) ? get_post_meta( $post_id, $_prefix.'status_pay', true ) : 'pending';
				

				foreach ($post_data_sanitize as $key => $value) {

					update_post_meta( $post_id, $key, $value );
				}

				$post_info = get_post( $post_id );

				$post_information = array(
					'ID' => $post_id,
					'post_title' =>  $name_event,
					'post_name' => '',
					'post_content' => $content_event,
					'post_type' => 'event',
					'post_status' => $post_info->post_status,
					'_thumbnail_id' => $img_thumbnail,
				);

				if( wp_update_post( $post_information ) ){

					do_action( 'el_vendor_after_update_event', $post_id );

					echo 'updated';	
				}else{
					echo 'error';
				}
				
				wp_die();

			} else { // Add new post

				// Check create event
				$check_create_event = el_check_create_event();
				switch ( $check_create_event['status'] ) {

					case 'false_total_event':
						echo 'false_total_event';
						wp_die();
						break;

					case 'false_time_membership':
						echo 'false_time_membership';
						wp_die();
						break;
						
					case 'error':
						echo 'error';
						wp_die();
						break;		
					
					default:
						break;
				}

				if( !el_can_publish_event() ){
					$event_status = 'pending';
					$post_data_sanitize[$_prefix.'event_active']   = 0;
				}else{
					$event_status = 'publish';
					$post_data_sanitize[$_prefix.'event_active']   = 1;
				}
				
				$post_data_sanitize['post_author']   = $current_user;
				$post_data_sanitize['post_title']    = $name_event;
				$post_data_sanitize['post_content']  = $content_event;
				$post_data_sanitize['post_type']     = 'event';
				$post_data_sanitize['post_status']   = apply_filters( 'el_admin_review_event', $event_status );
				$post_data_sanitize['_thumbnail_id'] = $img_thumbnail;

				$user_package = get_user_meta( $current_user, 'package', true );
				$post_data_sanitize[$_prefix.'package'] = $user_package;
				
				$new_post_id = wp_insert_post( $post_data_sanitize, true ); 

				//Cat
				if( !empty( $event_cat ) ){
					wp_set_post_terms( $new_post_id, $event_cat , 'event_cat' );
				}

				/* Custom Taxonomy */
				if( ! empty( $data_taxonomy ) ){
					foreach( $data_taxonomy as $slug_taxonomy => $val_taxonomy ) {
						wp_set_post_terms( $new_post_id, $val_taxonomy , $slug_taxonomy );
					}
				}

				// Tags
				if( !empty( $event_tag ) ){
					wp_set_post_terms( $new_post_id, $event_tag , 'event_tag' );
				}

				// Location
				$event_loc = array();
				if( $event_state && $event_state_obj = get_term_by('slug', $event_state, 'event_loc') ){
					$event_loc[] = $event_state_obj->term_id ? $event_state_obj->term_id : '';
				}

				if( $event_city && $event_city_obj = get_term_by('slug', $event_city, 'event_loc') ){
					$event_loc[] = $event_city_obj->term_id ? $event_city_obj->term_id : '';
				}

				wp_set_post_terms( $new_post_id, array_filter($event_loc) , 'event_loc' );

				/* Add New Status Pay */
				$post_data_sanitize[$_prefix.'status_pay'] = 'pending';

				foreach ($post_data_sanitize as $name => $value) {
					update_post_meta( $new_post_id, $name, $value );
				}

				do_action( 'el_vendor_after_create_event', $new_post_id );

				// Send Mail Create Event
				$receive_email_after_create_event = EL()->options->mail->get('receive_email_after_create_event', 'no');
				if ( $receive_email_after_create_event != 'no' ) {
					el_sendmail_create_event( $new_post_id );
				}

				$myaccount_page = get_myaccount_page();

				$redirect_link = add_query_arg( array(
								    'vendor' => 'listing-edit',
								    'id' => $new_post_id,
								), $myaccount_page );

				

				echo $redirect_link;
				wp_die();
			}
		}


		public function el_check_login_report(){
			$id_event = isset( $_POST['id_event'] ) ? sanitize_text_field( $_POST['id_event'] ) : '';
			if( is_user_logged_in() && $id_event ) {
				?>
				<div class="el_form_report">
				<form action="" >
					<div class="el_close">
						<span class="icon_close"></span>
					</div>
					<div class="el_row_input">
						<label for="el_message"><?php esc_html_e('Message', 'eventlist') ?></label>
						<textarea name="el_message" id="el_message" cols="30" rows="10"></textarea>
					</div>
					
					<div class="el-notify">
						<p class="success"><?php esc_html_e('Send mail success', 'eventlist') ?></p>
						<p class="error"><?php esc_html_e('Send mail failed', 'eventlist') ?></p>
						<p class="error-require"><?php esc_html_e('Please enter input field', 'eventlist') ?></p>
					</div>

					<div class="el_row_input">
						<button type="submit" class="submit-sendmail-report" data-id_event="<?php echo esc_attr( $id_event ) ?>" >
							<?php esc_html_e('Submit', 'eventlist') ?>
							<div class="submit-load-more">
								<div class="load-more">
									<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
								</div>
							</div>
						</button>
					</div>
				</form>
			</div>
			<?php
			} else {
				echo 'false';
			}
			wp_die();
		}

		/**
		 * Process Checkout
		 */
		public function el_check_user_login(){

			if( ! isset($_POST['data']) ) return false;
			if( !isset( $_POST['data']['el_next_event_nonce'] ) || !wp_verify_nonce( sanitize_text_field($_POST['data']['el_next_event_nonce']), 'el_next_event_nonce' ) ) return ;

			$setting_checkout_login = EL()->options->checkout->get('el_login_booking', 'no');

			if( $setting_checkout_login == 'yes' ) {
				if( is_user_logged_in() ) {
					echo 'true';
				} else {
					echo 'false';
				}
			} else {
				echo 'true';
			}

			wp_die();
		}

		/**
		 * Process Checkout
		 */
		public function el_process_checkout() {
			if ( ! isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];

			if ( ! isset( $post_data['el_checkout_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field($post_data['el_checkout_event_nonce']), 'el_checkout_event_nonce' ) ) return;

			if( $post_data['create_account'] == 'true' ){

				$user_id = el_create_account( $post_data );

				if( $user_id == false ){
					
					echo json_encode( array( 'el_message' => esc_html__( 'The email is exist, you can\'t make new account', 'eventlist' ) ) );
					wp_die();
				
				}else{

					$user = get_user_by( 'id', $user_id ); 
					wp_set_current_user($user_id);
	        		wp_set_auth_cookie($user_id, true);
	        		do_action( 'wp_login', $user->user_login, $user );

	        		// Send Mail to Reset Password
					el_mail_reset_password( $user_id );
				}

			}

			EL()->checkout->process_checkout( $_POST['data'] );
			
			wp_die();
		}

		/**
		 * Countdown Checkout
		 */
		public function el_countdown_checkout() {
			if ( !isset( $_POST['data'] ) ) wp_die();

			$post_data 	= $_POST['data'];
			$nonce 		= isset( $post_data['nonce'] ) ? sanitize_text_field( $post_data['nonce'] ) : '';
			$booking_id = isset( $post_data['booking_id'] ) ? sanitize_text_field( $post_data['booking_id'] ) : '';

			if ( !$nonce || !wp_verify_nonce( $nonce, 'el_countdown_checkout_nonce' ) ) return;

			if ( WC()->cart ) {
				WC()->cart->empty_cart();
			}

			echo 'success';
			wp_die();
		}

		/**
		 * Check discount
		 */
		public function el_check_discount() {
			if ( ! isset( $_POST['data'] ) ) wp_die();

			$post_data 		= $_POST['data'];
			$code_discount 	= sanitize_text_field( $post_data['code_discount'] );
			$id_event 		= sanitize_text_field( $post_data['id_event'] );
			$data 			= EL_Cart::instance()->check_code_discount( $id_event, $code_discount );

			echo $data;
			wp_die();
		}

		public function el_export_csv() {
			if( ! isset( $_POST['data'] ) ) {
				return false;
				wp_die();
			}

			$data = isset($_POST['data']) ? $_POST['data'] : [];
			$id_event = isset($data['id_event']) ? sanitize_text_field($data['id_event']) : '';
			$check_allow_export_attendees = check_allow_export_attendees_by_event($id_event);
			if (!$id_event || !verify_current_user_post($id_event) || $check_allow_export_attendees != 'yes' || !el_can_manage_booking() ) wp_die();

			$check_id_booking = isset($data['check_id_booking']) ? sanitize_text_field($data['check_id_booking']) : false;
			$check_event = isset($data['check_event']) ? sanitize_text_field($data['check_event']) : false;
			$check_calendar = isset($data['check_calendar']) ? sanitize_text_field($data['check_calendar']) : false;
			$check_name = isset($data['check_name']) ? sanitize_text_field($data['check_name']) : false;
			$check_phone = isset($data['check_phone']) ? sanitize_text_field($data['check_phone']) : false;
			$check_email = isset($data['check_email']) ? sanitize_text_field($data['check_email']) : false;
			$check_total_before_tax = isset($data['check_total_before_tax']) ? sanitize_text_field($data['check_total_before_tax']) : false;
			$check_total_after_tax = isset($data['check_total_after_tax']) ? sanitize_text_field($data['check_total_after_tax']) : false;
			$check_profit = isset($data['check_profit']) ? sanitize_text_field($data['check_profit']) : false;
			$check_commission = isset($data['check_commission']) ? sanitize_text_field($data['check_commission']) : false;
			$check_tax = isset($data['check_tax']) ? sanitize_text_field($data['check_tax']) : false;

			$check_coupon = isset($data['check_coupon']) ? sanitize_text_field($data['check_coupon']) : false;

			$check_status = isset($data['check_status']) ? sanitize_text_field($data['check_status']) : false;
			$check_ticket_type = isset($data['check_ticket_type']) ? sanitize_text_field($data['check_ticket_type']) : false;
			$check_extra_service = isset($data['check_extra_service']) ? sanitize_text_field($data['check_extra_service']) : false;

			$check_date_create = isset($data['check_date_create']) ? sanitize_text_field($data['check_date_create']) : false;

			$list_ckf_check = isset($data['list_ckf_check']) ? $data['list_ckf_check'] : [];

			$list_ckf_output = get_option( 'ova_booking_form', array() );

			$csv_row = [];

			if ($check_id_booking != 'false') {
				$csv_row[0][] = esc_html__("Booking ID", "eventlist");
			}

			if ($check_event != 'false') {
				$csv_row[0][] = esc_html__("Event", "eventlist");
			}

			if ($check_calendar != 'false') {
				$csv_row[0][] = esc_html__("Calendar", "eventlist");
			}

			if ($check_name != 'false') {
				$csv_row[0][] = esc_html__("Name", "eventlist");
			}

			if ($check_phone != 'false') {
				$csv_row[0][] = esc_html__("Phone", "eventlist");
			}

			if ($check_email != 'false') {
				$csv_row[0][] = esc_html__("Email", "eventlist");
			}

			
			if ($check_total_before_tax != 'false') {
				$csv_row[0][] = esc_html__("Total before tax", "eventlist");
			}

			if ($check_total_after_tax != 'false') {
				$csv_row[0][] = esc_html__("Total after tax", "eventlist");
			}

			if ($check_profit != 'false') {
				$csv_row[0][] = esc_html__("Profit", "eventlist");
			}

			if ($check_commission != 'false') {
				$csv_row[0][] = esc_html__("Commission", "eventlist");
			}

			if ($check_tax != 'false') {
				$csv_row[0][] = esc_html__("Tax", "eventlist");
			}

			if ($check_coupon != 'false') {
				$csv_row[0][] = esc_html__("Coupon", "eventlist");
			}
			

			if ($check_status != 'false') {
				$csv_row[0][] = esc_html__("Status", "eventlist");
			}

			if ($check_ticket_type != 'false') {
				$csv_row[0][] = esc_html__("Ticket Type", "eventlist");
			}
			
			if ($check_extra_service != 'false') {
				$csv_row[0][] = esc_html__("Extra Services", "eventlist");
			}

			if ($check_date_create != 'false') {
				$csv_row[0][] = esc_html__("Date Created", "eventlist");
			}

			if ( ! empty( $list_ckf_check ) && is_array( $list_ckf_check ) ) {
				foreach ( $list_ckf_check as $name_ckf ) {
					if ( isset( $list_ckf_output[$name_ckf] ) && ! empty( $list_ckf_output[$name_ckf] ) ) {
						$field = $list_ckf_output[$name_ckf];

						if ( isset( $field['enabled'] ) && $field['enabled'] == 'on' && isset( $field['label'] ) ) {
							$csv_row[0][] = html_entity_decode( $field['label'] );
						}
					}
				}
			}


			$agrs = [
				'post_type' => 'el_bookings',
				'post_status' => 'publish',
				"meta_query" => [
					'relation' => 'AND',
					[
						"key" => OVA_METABOX_EVENT . 'id_event',
						"value" => $id_event,
					],
					[
						"key" => OVA_METABOX_EVENT . 'status',
						"value" => apply_filters( 'el_export_booking_status', array( 'Completed' ) ),
						"between" => 'IN'
					],
				],
				'posts_per_page' => -1,
			];

			$list_booking_by_id_event = new WP_Query( $agrs );

			/* Write Data */
			$i = 0;
			if( $list_booking_by_id_event->have_posts() ): while( $list_booking_by_id_event->have_posts() ): $list_booking_by_id_event->the_post();

				global $post;
				$i++;

				if( $check_id_booking != 'false' ){
					$csv_row[$i][]= get_the_id();
				}

	    		// Event Name
				if( $check_event != 'false' ){
					$csv_row[$i][] = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'title_event', true ) );

				}

				// Calendar
				if( $check_calendar != 'false' ){
					$date = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'date_cal', true );
					$date = str_replace(",", " ", $date);
					$date = str_replace("#", " ", $date);

					// Date - Time
					$str_date = '';
					$ticket_ids = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'record_ticket_ids', true );

					if ( isset( $ticket_ids[0] ) && $ticket_ids[0] ) {
						$ticket_id = $ticket_ids[0];

						$date_start 	= get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_start', true );
						$date_end 		= get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_end', true );
						$date_format 	= get_option('date_format');
						$time_format 	= get_option('time_format');

						if ( absint( $date_start ) && absint( $date_end ) ) {
							$str_start_date = date_i18n( $date_format, $date_start );
							$str_start_time = date_i18n( $time_format, $date_start );

							$str_end_date 	= date_i18n( $date_format, $date_end );
							$str_end_time 	= date_i18n( $time_format, $date_end );
							
							if ( $str_start_date == $str_end_date ) {
								$str_date = $str_start_date . ' ' . $str_start_time . ' - ' . $str_end_time;
							} else {
								$str_date = $str_start_date . ' ' . $str_start_time . ' - ' . $str_end_date . ' ' . $str_end_time;
							}
						}
					}

					if ( $str_date ) {
						$str_date = str_replace(",", " ", $str_date);
						$str_date = str_replace("#", " ", $str_date);

						$csv_row[$i][] = $str_date;
					} else {
						$csv_row[$i][] = $date;
					}
				}

				//Name Customer
				if( $check_name != 'false' ){
					$name = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'name', true );
					$name = str_replace(",", " ", $name);
					$name = str_replace("#", " ", $name);
					$csv_row[$i][] = html_entity_decode( $name );
				}

				//Phone Customer
				if( $check_phone != 'false' ){
					$phone = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'phone', true );
					$phone = str_replace(",", " ", $phone);
					$phone = str_replace("#", " ", $phone);
					$csv_row[$i][] = html_entity_decode( $phone );

				}

				//Email Customer
				if( $check_email != 'false' ){
					$email = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'email', true );
					$email = str_replace(",", " ", $email);
					$email = str_replace("#", " ", $email);
					$csv_row[$i][] = html_entity_decode( $email );

				}

				//Total before tax
				if( $check_total_before_tax != 'false' ){
					$total_before_tax = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'total', true );
					$total_before_tax = str_replace(",", " ", $total_before_tax);
					$csv_row[$i][] = $total_before_tax;

				}

				//Total after tax
				if( $check_total_after_tax != 'false' ){
					$total_after_tax = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'total_after_tax', true );
					$total_after_tax = str_replace(",", " ", $total_after_tax);
					$csv_row[$i][] = $total_after_tax;

				}

				// Profit
				if( $check_profit != 'false' ){
					
					if( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'profit', true ) ){ // Use from version 1.3.7
						$profit = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'profit', true );
					}else{
						$profit = EL_Booking::instance()->get_profit_by_id_booking( $post->ID );	
					}

					$csv_row[$i][] = $profit;

				}

				// Commission
				if( $check_commission != 'false' ){
					
					if( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'commission', true ) ){ // Use from version 1.3.7
						$commission = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'commission', true );
					}else{
						$commission = EL_Booking::instance()->get_commission_by_id_booking( $post->ID );	
					}

					$csv_row[$i][] = $commission;

				}


				// Tax
				if( $check_tax != 'false' ){
					
					if( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'tax', true ) ){ // Use from version 1.3.7
						$tax = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'tax', true );
					}else{
						$tax = EL_Booking::instance()->get_tax_by_id_booking( $post->ID );	
					}

					$csv_row[$i][] = $tax;

				}

				// Coupon
				if( $check_coupon != 'false' ){
					
					if( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'coupon', true ) ){ // Use from version 1.3.7
						$coupon = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'coupon', true );
					}else{
						$coupon = '';
					}

					$csv_row[$i][] = $coupon;

				}

				//status
				if( $check_status != 'false' ){
					$status = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'status', true );
					$status = str_replace(",", " ", $status);
					$csv_row[$i][] = $status;

				}

				//Ticket type
				if( $check_ticket_type != 'false' ){
					$seat_option = get_post_meta( $id_event, OVA_METABOX_EVENT . 'seat_option', true);

					if ( $seat_option != 'map' ) {

						$list_ticket_in_event = get_post_meta( $id_event, OVA_METABOX_EVENT . 'ticket', true);

						$list_ticket = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'list_id_ticket', true );
						$ticket_qty = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'list_qty_ticket_by_id_ticket', true );
						$list_ticket = json_decode($list_ticket);


						$ticket_name = "";
						$ticket_text = __( ' ticket(s)', 'eventlist' );

						if ( ! empty($list_ticket_in_event) && is_array($list_ticket_in_event) ) {
							foreach ($list_ticket_in_event as $ticket) {
								if ( in_array($ticket['ticket_id'], $list_ticket) ) {
									$ticket_name .= $ticket['name_ticket']." - ".$ticket_qty[$ticket['ticket_id']].$ticket_text."; ";
								}
							}
						}
						$ticket_name = str_replace(",", " ", $ticket_name);
						$ticket_name = substr(trim($ticket_name), 0, -1);
						$csv_row[$i][] = html_entity_decode( $ticket_name );

					} else {
						$cart = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'cart', true);
						$ticket_type = el_ticket_type_seat_map_cart( $cart );
						$csv_row[$i][] = html_entity_decode( $ticket_type );
					}

				}

				if ( $check_extra_service != 'false' ) {
					$extra_service = get_post_meta( $post->ID, OVA_METABOX_EVENT.'extra_service', true );
					$data_extra_service = el_extra_sv_get_info_booking( $extra_service );
					$data_extra_service = strip_tags( $data_extra_service );
					$data_extra_service = str_replace( ', ', '; ', $data_extra_service );
					$csv_row[$i][] = html_entity_decode( $data_extra_service );
				}

				if( $check_date_create != 'false' ){
					$date_format = get_option('date_format');
					$time_format = get_option('time_format');
					$time = get_the_date($date_format, $post->ID) . " - " . get_the_date($time_format, $post->ID);

					$time = str_replace(",", " ", $time);

					$csv_row[$i][] = $time;
				}

				if ( ! empty( $list_ckf_check ) && is_array( $list_ckf_check ) ) {
					$data_checkout_field 		= get_post_meta( $post->ID, OVA_METABOX_EVENT . 'data_checkout_field', true );
					$arr_data_checkout_field 	= json_decode( $data_checkout_field, true );

					foreach ( $list_ckf_check as $name_ckf ) {
						if ( isset( $list_ckf_output[$name_ckf] ) && ! empty( $list_ckf_output[$name_ckf] ) ) {
							$field = $list_ckf_output[$name_ckf];

							if ( isset( $field['enabled'] ) && $field['enabled'] == 'on' && isset( $field['label'] ) ) {
								if ( isset( $arr_data_checkout_field[$name_ckf] ) && $arr_data_checkout_field[$name_ckf] ) {
									$csv_row[$i][] = html_entity_decode( $arr_data_checkout_field[$name_ckf] );
								} else {
									$csv_row[$i][] = '';
								}
							}
						}
					}
				}

			endwhile;endif;

			echo json_encode($csv_row);
			wp_die();
		}

		public function export_csv_ticket() {
			if( ! isset( $_POST['data'] ) ) {
				return false;
				wp_die();
			}

			$data = isset($_POST['data']) ? $_POST['data'] : [];
			$id_event = isset($data['id_event']) ? sanitize_text_field($data['id_event']) : '';
			$check_allow_export_tickets = check_allow_export_tickets_by_event($id_event);

			if (!$id_event || !verify_current_user_post($id_event) || !el_can_manage_ticket() ) wp_die();

			$check_event = isset($data['check_event']) ? sanitize_text_field($data['check_event']) : false;
			$check_booking_id = isset($data['check_booking_id']) ? sanitize_text_field($data['check_booking_id']) : false;
			$check_ticket_type = isset($data['check_ticket_type']) ? sanitize_text_field($data['check_ticket_type']) : false;
			$check_extra_service = isset($data['check_extra_service']) ? sanitize_text_field($data['check_extra_service']) : false;
			$check_name = isset($data['check_name']) ? sanitize_text_field($data['check_name']) : false;
			$check_phone_customer = isset($data['check_phone_customer']) ? sanitize_text_field($data['check_phone_customer']) : false;
			$check_email_customer = isset($data['check_email_customer']) ? sanitize_text_field($data['check_email_customer']) : false;
			$check_address_customer = isset($data['check_address_customer']) ? sanitize_text_field($data['check_address_customer']) : false;
			$check_venue = isset($data['check_venue']) ? sanitize_text_field($data['check_venue']) : false;
			$check_address = isset($data['check_address']) ? sanitize_text_field($data['check_address']) : false;
			$check_seat = isset($data['check_seat']) ? sanitize_text_field($data['check_seat']) : false;
			$check_qr_code = isset($data['check_qr_code']) ? sanitize_text_field($data['check_qr_code']) : false;
			$check_start_date = isset($data['check_start_date']) ? sanitize_text_field($data['check_start_date']) : false;
			$check_end_date = isset($data['check_end_date']) ? sanitize_text_field($data['check_end_date']) : false;
			$check_date_create = isset($data['check_date_create']) ? sanitize_text_field($data['check_date_create']) : false;
			$checkin_time = isset($data['checkin_time']) ? sanitize_text_field($data['checkin_time']) : false;
			$ticket_checked = isset($data['ticket_checked']) ? sanitize_text_field($data['ticket_checked']) : false;
			$ticket_price = isset($data['ticket_price']) ? sanitize_text_field($data['ticket_price']) : false;
			// $check_checkout_field = isset($data['check_checkout_field']) ? sanitize_text_field($data['check_checkout_field']) : false;

			$list_ckf_check = isset($data['list_ckf_check']) ? $data['list_ckf_check'] : [];

			$list_ckf_output = get_option( 'ova_booking_form', array() );


			$csv_row = [];

			if ($check_event != 'false') {
				$csv_row[0][] = esc_html__("Event", "eventlist");
			}

			if ($check_booking_id != 'false') {
				$csv_row[0][] = esc_html__("Booking ID", "eventlist");
			}

			if ($check_ticket_type != 'false') {
				$csv_row[0][] = esc_html__("Ticket Type", "eventlist");
			}

			if ($check_extra_service != 'false') {
				$csv_row[0][] = esc_html__("Extra Services", "eventlist");
			}

			if ($check_name != 'false') {
				$csv_row[0][] = esc_html__("Name", "eventlist");
			}

			if ($check_phone_customer != 'false') {
				$csv_row[0][] = esc_html__("Phone", "eventlist");
			}

			if ($check_email_customer != 'false') {
				$csv_row[0][] = esc_html__("Email", "eventlist");
			}

			if ($check_address_customer != 'false') {
				$csv_row[0][] = esc_html__("Address Customer", "eventlist");
			}

			if ($check_venue != 'false') {
				$csv_row[0][] = esc_html__("Venue", "eventlist");
			}

			if ($check_address != 'false') {
				$csv_row[0][] = esc_html__("Address", "eventlist");
			}

			if ($check_seat != 'false') {
				$csv_row[0][] = esc_html__("Seat", "eventlist");
			}

			if ($check_qr_code != 'false') {
				$csv_row[0][] = esc_html__("Qr Code", "eventlist");
			}

			if ($check_start_date != 'false') {
				$csv_row[0][] = esc_html__("Start date", "eventlist");
			}

			if ($check_end_date != 'false') {
				$csv_row[0][] = esc_html__("End date", "eventlist");
			}

			if ($check_date_create != 'false') {
				$csv_row[0][] = esc_html__("Date Created", "eventlist");
			}

			if ($checkin_time != 'false') {
				$csv_row[0][] = esc_html__("Checkin time", "eventlist");
			}

			if ($ticket_checked != 'false') {
				$csv_row[0][] = esc_html__("Ticket checked", "eventlist");
			}

			if ($ticket_price != 'false') {
				$csv_row[0][] = esc_html__("Price", "eventlist");
			}

			if ( ! empty( $list_ckf_check ) && is_array( $list_ckf_check ) ) {
				foreach ( $list_ckf_check as $name_ckf ) {
					if ( isset( $list_ckf_output[$name_ckf] ) && ! empty( $list_ckf_output[$name_ckf] ) ) {
						$field = $list_ckf_output[$name_ckf];

						if ( isset( $field['enabled'] ) && $field['enabled'] == 'on' && isset( $field['label'] ) ) {
							$csv_row[0][] = html_entity_decode( $field['label'] );
						}
					}
				}
			}

			$agrs = [
				'post_type' => 'el_tickets',
				'post_status' => 'publish',
				"meta_query" => [
					'relation' => 'AND',
					[
						"key" => OVA_METABOX_EVENT . 'event_id',
						"value" => $id_event,
					],
				],
				'posts_per_page' => -1,
			];

			$list_ticket_record_by_id_event = new WP_Query( $agrs );


			/* Write Data */
			$i = 0;
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
			$str_data_ckf = '';

			if( $list_ticket_record_by_id_event->have_posts() ): while( $list_ticket_record_by_id_event->have_posts() ): $list_ticket_record_by_id_event->the_post();

				global $post;
				$i++;


	    		// Event Name
				if( $check_event != 'false' ){
					$csv_row[$i][] = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'name_event', true );

				}

				// Booking ID
				if( $check_booking_id != 'false' ){
					$csv_row[$i][] = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'booking_id', true );

				}

				//Ticket type
				if( $check_ticket_type != 'false' ){
					$ticket_name = html_entity_decode( get_the_title( $post->ID ) );
					$ticket_name = str_replace(",", " ", $ticket_name);
					$csv_row[$i][] = $ticket_name;
				}
				// Extra service
				if ( $check_extra_service != 'false' ) {
					$extra_service 		= get_post_meta( $post->ID, OVA_METABOX_EVENT.'extra_service', true );
					$data_extra_service = el_extra_sv_ticket( $extra_service );
					$data_extra_service = strip_tags( $data_extra_service );
					$data_extra_service = str_replace(', ', '; ', $data_extra_service );
					$csv_row[$i][] = html_entity_decode( $data_extra_service );
				}

				//Name Customer
				if( $check_name != 'false' ){
					$name = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'name_customer', true ) );
					$name = str_replace(",", " ", $name);
					$name = str_replace("#", " ", $name);
					$csv_row[$i][] = $name;
				}

				//Phone Customer
				if( $check_phone_customer != 'false' ){
					$phone_customer = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'phone_customer', true ) );
					$phone_customer = str_replace(",", " ", $phone_customer);
					$csv_row[$i][] = $phone_customer;
				}

				//Email Customer
				if( $check_email_customer != 'false' ){
					$email_customer = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'email_customer', true ) );
					$email_customer = str_replace(",", " ", $email_customer);
					$csv_row[$i][] = $email_customer;
				}

				//Address Customer
				if( $check_address_customer != 'false' ){
					$address_customer = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'address_customer', true ) );
					$address_customer = str_replace(",", " ", $address_customer);
					$address_customer = str_replace("#", " ", $address_customer);
					$csv_row[$i][] = $address_customer;
				}

				//Venue
				if( $check_venue != 'false' ){
					$arr_venue = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'venue', true );
					$venue = is_array( $arr_venue ) ? implode("; ", $arr_venue) : $arr_venue;
					$venue = str_replace(",", " ", $venue);
					$venue = str_replace("#", " ", $venue);

					$csv_row[$i][] = html_entity_decode( $venue );
				}

				//Address
				if( $check_address != 'false' ){
					$address = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'address', true ) );
					$address = str_replace(",", " ", $address);
					$address = str_replace("#", " ", $address);
					$csv_row[$i][] = $address;
				}

				//Seat
				if( $check_seat != 'false' ){
					$seat = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'seat', true ) );
					$seat = str_replace(",", " ", $seat);
					$seat = str_replace("#", " ", $seat);
					$person_type = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'person_type', true ) );
					if ( $person_type ) {
						$seat.= ' - '.$person_type;
					}
					$csv_row[$i][] = $seat;
				}

				//Qr code
				if( $check_qr_code != 'false' ){
					$qr_code = html_entity_decode( get_post_meta( $post->ID, OVA_METABOX_EVENT . 'qr_code', true ) );
					$qr_code = str_replace(",", " ", $qr_code);
					$csv_row[$i][] = $qr_code;
				}

				//Date start
				if( $check_start_date != 'false' ){
					$date_start = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'date_start', true );
					$time_start = date_i18n($date_format, $date_start). " - " . date_i18n($time_format, $date_start);

					$time_start = str_replace(",", " ", $time_start);
					$csv_row[$i][] = $time_start;
				}

				//Date end
				if( $check_end_date != 'false' ){
					$date_end = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'date_end', true );
					$time_end = date_i18n($date_format, $date_end) . " - " . date_i18n($time_format, $date_end);

					$time_end = str_replace(",", " ", $time_end);
					$csv_row[$i][] = $time_end;
				}


				if( $check_date_create != 'false' ){

					$time = get_the_date($date_format, $post->ID) . " - " . get_the_date($time_format, $post->ID);

					$time = str_replace(",", " ", $time);

					$csv_row[$i][] = $time;
				}

				// checkin time
				if( $checkin_time != 'false' ){

					$checkin_time = get_post_meta( $post->ID, OVA_METABOX_EVENT . 'checkin_time', true );

					if( $checkin_time ){

						$time = date( $date_format.' - '.$time_format, $checkin_time );
						$csv_row[$i][] = str_replace(",", " ", $time); ;
					}
					else{
						$csv_row[$i][] = $checkin_time;
					}
					
				}

				if ( $ticket_checked != 'false' ) {
					$ticket_checked = get_post_meta( $post->ID, OVA_METABOX_EVENT.'ticket_status', true );
					if ( $ticket_checked == 'checked' ) {
						$csv_row[$i][] = str_replace(",", " ", $ticket_checked);
					} else {
						$csv_row[$i][] = "";
					}
				}

				if ( $ticket_price != 'false' ) {
					$ticket_price 	= get_post_meta( $post->ID, OVA_METABOX_EVENT.'price_ticket', true );
					$booking_id 	= get_post_meta( $post->ID, OVA_METABOX_EVENT.'booking_id', true );

					if ( ! $ticket_price ) {
						$cart 			= get_post_meta( $booking_id, OVA_METABOX_EVENT.'cart', true );
						$seat 			= get_post_meta( $post->ID, OVA_METABOX_EVENT.'ticket_id_event', true );
						$person_type 	= get_post_meta( $post->ID, OVA_METABOX_EVENT.'person_type', true );

						if ( ! empty( $cart ) ) {
							foreach ($cart as $key => $value) {
								if ( $value['id'] == $seat ) {

									if ( isset( $value['data_person'] ) && ! empty( $value['data_person'] && $person_type ) ) {
										foreach  ( $value['data_person'] as $k => $val ) {
											if ( $val['name'] == $person_type ) {
												$ticket_price = $val['price'];
											}
										}	
									} else {
										$ticket_price = isset( $value['price'] ) ? $value['price'] : '';
									}
									
								}
							}
						}
					}

					if ( $ticket_price ) {
						$csv_row[$i][] = html_entity_decode( el_price( $ticket_price ), ENT_HTML5, 'utf-8');
					} else {
						$csv_row[$i][] = html_entity_decode( el_price( 0 ), ENT_HTML5, 'utf-8');
					}
				}

				if ( ! empty( $list_ckf_check ) && is_array( $list_ckf_check ) ) {
					$data_checkout_field 		= get_post_meta( $post->ID, OVA_METABOX_EVENT . 'data_checkout_field', true );
					$arr_data_checkout_field 	= json_decode( $data_checkout_field, true );

					foreach ( $list_ckf_check as $name_ckf ) {
						if ( isset( $list_ckf_output[$name_ckf] ) && ! empty( $list_ckf_output[$name_ckf] ) ) {
							$field = $list_ckf_output[$name_ckf];

							if ( isset( $field['enabled'] ) && $field['enabled'] == 'on' && isset( $field['label'] ) ) {
								if ( isset( $arr_data_checkout_field[$name_ckf] ) && $arr_data_checkout_field[$name_ckf] ) {
									$csv_row[$i][] = html_entity_decode( $arr_data_checkout_field[$name_ckf] );
								} else {
									$csv_row[$i][] = '';
								}
							}
						}
					}
				}

			endwhile;endif;

			echo json_encode($csv_row);
			wp_die();
		}

		public function el_add_package() {

			if( !isset( $_POST['data'] ) ) wp_die();


			$post_data = $_POST['data'];
			$user_id = wp_get_current_user()->ID;

			// check user login
			if( ! $user_id ) { 
				echo json_encode( array(
					'code' => 0, 
					'status' => esc_html__('You have to login','eventlist'),
					'url'	=> wp_login_url()
				) ); wp_die(); 
			}

			$pid = isset( $post_data['pid'] ) ? (int)$post_data['pid'] : '';
			$payment_method = isset( $post_data['payment_method'] ) ? sanitize_text_field( $post_data['payment_method'] ) : '';
			$package = get_post_meta( $pid, OVA_METABOX_EVENT.'package_id', true );

			$can_add_package = apply_filters( 'el_can_add_package', true, $pid );

			$response_can_not_add = array(
				'code' => 0, 
				'status' => esc_html__('Can\'t add membership' ,'eventlist'),
				'url'	=> get_myaccount_page()
			);

			// check user can register package
			if( $pid && $can_add_package ){

				// Add to membership table
				$membership_id = EL_Package::instance()->add_membership( $pid, $user_id );
				// if success
				if( $membership_id ){

					$fee_register_package = get_post_meta( $pid, OVA_METABOX_EVENT.'fee_register_package', true );

					if( $fee_register_package ){
						// get payment gateway
						if ( ! empty( $payment_method ) ) {
							$response = array(
								'payment_method' 	=> $payment_method,
								'code' 				=> $package,
								'membership_id' 	=> $membership_id,
							);
							echo json_encode( $response );
						} else {
							echo json_encode( $response_can_not_add );
						}
					// If free
					} else {
						
						update_user_meta( $user_id, 'package', $package );
						$membership = array(
							'ID'           => $membership_id,
							'post_status'   => 'Publish',
							'meta_input'	=> array(
								OVA_METABOX_EVENT.'payment' => 'free',
							)
						);

						$check_update = wp_update_post( $membership );

						if (  is_wp_error( $check_update ) ) {
							echo json_encode( array(
								'code' => 0,
								'status' => esc_html__('Update Failed','eventlist'), 
								'url'	=>  add_query_arg( array( 
									'vendor' => 'package'
								),
								get_myaccount_page() )
							) );
						} else {
							echo json_encode( array(
								'code' => $package,
								'status' => esc_html__('Update Success','eventlist'), 
								'url'	=>  add_query_arg( array( 
									'vendor' => 'package'
								),
								get_myaccount_page() )
							) );
						}

						
					}


				}else{
					echo json_encode( $response_can_not_add );
				}
			
			}else{
				echo json_encode( array(
					'code' => 0, 
					'status' => esc_html__('You dont have permission to add package','eventlist'),
					'url'	=> '#'
				) ); wp_die(); 
			}


			wp_die();
		}

		public function el_add_wishlist() {
			if( !isset( $_POST['data'] ) ) wp_die();
			$post_data = isset($_POST['data']) ? $_POST['data'] : [];
			$id_event = sanitize_text_field($post_data['id_event']);
			if (empty($id_event)) wp_die();

			$cookie_name = "el_wl_event";
			$cookie_value = json_encode([$id_event]);
			$current_time = current_time("timestamp");


			if (!isset($_COOKIE['el_wl_event'])) {
				setcookie($cookie_name, $cookie_value, $current_time + (86400 * 30), "/");
			} else {
				$value_cookie = $_COOKIE['el_wl_event'];
				$value_cookie = str_replace("\\", "", $value_cookie);
				$value_cookie = json_decode($value_cookie, true);

				if (!empty($value_cookie) && is_array($value_cookie) && !in_array($id_event, $value_cookie)) {
					array_push($value_cookie, $id_event);
				}

				$cookie_value = json_encode($value_cookie);
				setcookie($cookie_name, $cookie_value, $current_time + (86400 * 30), "/");

			}

			wp_die(); 
		}

		public function el_remove_wishlist() {
			if( !isset( $_POST['data'] ) ) wp_die();
			$post_data = isset($_POST['data']) ? $_POST['data'] : [];
			$id_event = sanitize_text_field($post_data['id_event']);

			$cookie_name = "el_wl_event";
			$current_time = current_time("timestamp");

			if (empty($id_event)) wp_die();

			if (isset($_COOKIE['el_wl_event'])) {

				$value_cookie = $_COOKIE['el_wl_event'];
				$value_cookie = str_replace("\\", "", $value_cookie);
				$value_cookie = json_decode($value_cookie, true);

				if (!empty($value_cookie) && is_array($value_cookie) && in_array($id_event, $value_cookie)) {
					$value_cookie = array_diff($value_cookie, [$id_event]);
				}
				if (empty($value_cookie)) {
					setcookie($cookie_name, $cookie_value, -3600, "/");
				} else {
					$cookie_value = json_encode($value_cookie);
					setcookie($cookie_name, $cookie_value, $current_time + (86400 * 30), "/");
				}

			}

			wp_die();
		}

		/* Update Bank */
		public static function el_update_payout_method() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$_prefix = OVA_METABOX_EVENT;

			$post_data = $_POST['data'];

			$user_id = wp_get_current_user()->ID;

			if( !isset( $post_data['el_update_payout_method_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_update_payout_method_nonce'] ), 'el_update_payout_method_nonce' ) ) return ;


            $payout_method  = isset( $post_data['payout_method'] ) ? sanitize_text_field( $post_data['payout_method'] ) : '';
			$user_bank_owner  = isset( $post_data['user_bank_owner'] ) ? sanitize_text_field( $post_data['user_bank_owner'] ) : '';
			$user_bank_number = isset( $post_data['user_bank_number'] ) ? sanitize_user( $post_data['user_bank_number'] ) : '';
			$user_bank_name   = isset( $post_data['user_bank_name'] ) ? sanitize_text_field( $post_data['user_bank_name'] ) : '';
			$user_bank_branch = isset( $post_data['user_bank_branch'] ) ? sanitize_text_field( $post_data['user_bank_branch'] ) : '';
			$user_bank_routing = isset( $post_data['user_bank_routing'] ) ? sanitize_text_field( $post_data['user_bank_routing'] ) : '';
			$user_bank_paypal_email = isset( $post_data['user_bank_paypal_email'] ) ? sanitize_text_field( $post_data['user_bank_paypal_email'] ) : '';
			$user_bank_stripe_account = isset( $post_data['user_bank_stripe_account'] ) ? sanitize_text_field( $post_data['user_bank_stripe_account'] ) : '';
			$user_bank_iban = isset( $post_data['user_bank_iban'] ) ? sanitize_text_field( $post_data['user_bank_iban'] ) : '';
			$user_bank_swift_code = isset( $post_data['user_bank_swift_code'] ) ? sanitize_text_field( $post_data['user_bank_swift_code'] ) : '';
			$user_bank_ifsc_code = isset( $post_data['user_bank_ifsc_code'] ) ? sanitize_text_field( $post_data['user_bank_ifsc_code'] ) : '';
			$data_payout_method_field = isset( $post_data['data_payout_method_field'] ) ? sanitize_list_checkout_field( $post_data['data_payout_method_field'] ) : [];

			$post_data = array( 
				'user_bank_owner'  => $user_bank_owner,
				'user_bank_number' => $user_bank_number,
				'user_bank_name'   => $user_bank_name,
				'user_bank_branch' => $user_bank_branch,
				'user_bank_routing' => $user_bank_routing,
				'user_bank_paypal_email' => $user_bank_paypal_email,
				'user_bank_stripe_account' => $user_bank_stripe_account,
				'user_bank_iban' => $user_bank_iban,
				'user_bank_swift_code' => $user_bank_swift_code,
				'user_bank_ifsc_code' => $user_bank_ifsc_code,
				'payout_method' => $payout_method,
				'data_payout_method_field' => json_encode( $data_payout_method_field, JSON_UNESCAPED_UNICODE ),
			);

			foreach($post_data as $key => $value) {
				update_user_meta( $user_id, $key, $value );
			}
			echo true;
			wp_die();
		}


		/* Add withdrawal */
		public static function el_add_withdrawal() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$_prefix = OVA_METABOX_EVENT;

			$post_data = $_POST['data'];

			$user_id = wp_get_current_user()->ID;
			if (empty($user_id)) die();

			if( !isset( $post_data['el_add_withdrawal_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_add_withdrawal_nonce'] ), 'el_add_withdrawal_nonce' ) ) return ;
            
			$amount  = isset( $post_data['amount'] ) ? floatval( $post_data['amount'] ) : '';
			

			$total_earning  = EL_Payout::instance()->get_total_profit( $user_id );
			$total_amount_payout = EL_Payout::instance()->get_total_amount_payout( $user_id );

			$withdrawable = $total_earning - $total_amount_payout;
     

			if( $amount == null || $amount == "") {

				echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'First name must be filled out', 'eventlist' ) ) );
				wp_die();

			} else if( !is_numeric( $amount ) || $amount < 0 ){

				echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'Amount must Number and more than 0', 'eventlist' ) ) );
				wp_die();

			} else if( ( $amount - $withdrawable ) > 0.00000000000001 ){
				echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'Amount must be less than', 'eventlist' ).' '.$withdrawable ) );
				wp_die();				

			}else{

				$payout_method = get_user_meta( $user_id, 'payout_method', true );
				$meta_payout_method = [];
				switch ( $payout_method ) {
					case 'bank':
						$meta_payout_method[ $_prefix.'user_bank_owner' ] 		= get_user_meta( $user_id, 'user_bank_owner', true );
						$meta_payout_method[ $_prefix.'user_bank_number' ] 		= get_user_meta( $user_id, 'user_bank_number', true );
						$meta_payout_method[ $_prefix.'user_bank_name' ] 		= get_user_meta( $user_id, 'user_bank_name', true );
						$meta_payout_method[ $_prefix.'user_bank_branch' ] 		= get_user_meta( $user_id, 'user_bank_branch', true );
						$meta_payout_method[ $_prefix.'user_bank_routing' ] 	= get_user_meta( $user_id, 'user_bank_routing', true );
						$meta_payout_method[ $_prefix.'user_bank_iban' ] 		= get_user_meta( $user_id, 'user_bank_iban', true );
						$meta_payout_method[ $_prefix.'user_bank_swift_code' ] 	= get_user_meta( $user_id, 'user_bank_swift_code', true );
						$meta_payout_method[ $_prefix.'user_bank_ifsc_code' ] 	= get_user_meta( $user_id, 'user_bank_ifsc_code', true );
						break;
					case 'paypal':	
						$meta_payout_method[ $_prefix.'user_bank_paypal_email' ] = get_user_meta( $user_id, 'user_bank_paypal_email', true );
						break;
					default:
					    $meta_payout_method[ $_prefix.'data_payout_method_field' ] = get_user_meta( $user_id, 'data_payout_method_field', true );
					    break;
					
				}

				$post_data['post_type'] = 'payout';
				$post_data['post_status'] = 'publish';
				$post_data['post_author'] = $user_id;

				$meta_custom_fields = array(
					$_prefix.'amount'  => $amount,
					$_prefix.'time' => current_time( 'timestamp' ),
					$_prefix.'withdrawal_status' => 'Pending',
					$_prefix.'payout_method'	=> $payout_method
				);

				$meta_input = array_merge( $meta_custom_fields, $meta_payout_method );

				$post_data['meta_input'] = apply_filters( 'el_payout_metabox_input', $meta_input );

				// Get all bookings doesn't payout yet
				$bookings = EL_Booking::instance()->get_bookings_do_not_payout( $user_id );

				if( $bookings->have_posts() ) : while ( $bookings->have_posts() ) : $bookings->the_post();

					$booking_id = get_the_id();

					// Update Profit Status of booking to 'Waiting'
					update_post_meta( $booking_id, $_prefix.'profit_status', 'Waiting' );

				endwhile; endif; wp_reset_postdata();


				if( $payout_id = wp_insert_post( $post_data, true ) ){
					//update title booking
					$arr_post = [
						'ID' => $payout_id,
						'post_title' => $payout_id ,
					];
					wp_update_post($arr_post);

					// handle send mail to admin
					if ( el_enable_send_withdrawal_email() === true ) {
						$send_mail_to_admin = el_send_mail_admin_payout_request( $user_id, $payout_id, $amount, $payout_method );
						if ( ! $send_mail_to_admin ) {
							echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'Error sending email to admin', 'eventlist' ) ) );
							wp_die();
						}
					}
					
					return $payout_id;
					wp_die();

				}else{
					return;
					wp_die();
				}

			}
		}


		/* Load Location Search */
		public static function el_load_location_search() {
			$keyword = isset($_POST['keyword']) ? sanitize_text_field( $_POST['keyword'] ) : '';

			$args = array(
				'taxonomy'   => 'event_loc',
				'orderby'    => 'id', 
				'order'      => 'ASC',
				'hide_empty' => false,
				'fields'     => 'all',
				'name__like' => $keyword,
			); 

			$terms = get_terms( $args );

			$count = count($terms);
			if($count > 0){
				$value = array();
				foreach ($terms as $term) {
					$value[] = $term->name;
				}
			}

			echo json_encode($value);

			wp_die();
		}

		public static function el_search_map() {
			if( !isset( $_POST['data'] ) ) wp_die();
			$_prefix = OVA_METABOX_EVENT;

			$post_data = $_POST['data'];

			$map_lat = isset( $post_data['map_lat'] ) ? floatval( $post_data['map_lat'] ) : '';
			$map_lng = isset( $post_data['map_lng'] ) ? floatval( $post_data['map_lng'] ) : '';
			$radius = isset( $post_data['radius'] ) ? floatval( $post_data['radius'] ) : '';
			$radius_unit = isset( $post_data['radius_unit'] ) ? sanitize_text_field( $post_data['radius_unit'] ) : 'km';
			$show_featured = isset( $post_data['show_featured'] ) ? sanitize_text_field( $post_data['show_featured'] ) : '';

			/***** Query Radius *****/
			$args_query_radius = array(
				'post_type' => 'event',
				'posts_per_page' => -1,
			);

			/* Show Featured */
			if ($show_featured == 'yes') {
				$args_featured = array(
					'meta_key' =>  OVA_METABOX_EVENT.'event_feature',
					'meta_query'=> array(
						array(
							'key' =>  OVA_METABOX_EVENT.'event_feature',
							'compare' => '=',
							'value' => 'yes',
						)
					)
				);
			} else {
				$args_featured = array();
			}



			$args_query_radius2 = array_merge( $args_query_radius,$args_featured  );

			$the_query = new WP_Query( $args_query_radius2);

			$results = array();

			$arr_distance = array();

			$posts = $the_query->get_posts();

			if ($map_lat != '' || $map_lng != '') {
				foreach($posts as $post)  {
					/* Latitude Longitude Search */
					$lat_search = deg2rad($map_lat);
					$lng_search = deg2rad($map_lng);

					/* Latitude Longitude Post */
					$lat_post = deg2rad( floatval( get_post_meta( $post->ID, OVA_METABOX_EVENT.'map_lat', true ) ) );
					$lng_post = deg2rad( floatval( get_post_meta( $post->ID, OVA_METABOX_EVENT.'map_lng', true ) ) );

					$lat_delta = $lat_post - $lat_search;
					$lon_delta = $lng_post - $lng_search;

					// $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) + cos($lat_search) * cos($lat_post) * pow(sin($lon_delta / 2), 2)));
					$angle = acos(sin($lat_search) * sin($lat_post) + cos($lat_search) * cos($lat_post) * cos($lng_search - $lng_post));

					/* 6371 = the earth's radius in km */
					/* 3959 = the earth's radius in mi */
					$distance =  6371 * $angle;

					if ( 'mi' === $radius_unit ) {
						$distance =  3959 * $angle;
					}

					if( $distance <= $radius || !$map_lat ) {
						array_push($arr_distance, $distance);
						array_push( $results, $post->ID );
					}
				}

				wp_reset_postdata();
				array_multisort($arr_distance, $results);

			} else {
				foreach($posts as $post)  {
					array_push( $results, $post->ID );
				}
			}

			if ( $map_lat && !$results ) {
				$results = array('');
			}
			/***** End Query Radius *****/


			/***** Query Post in Radius *****/
			$orderby = EL()->options->event->get('archive_order_by') ? EL()->options->event->get('archive_order_by') : 'title';
			$order = EL()->options->event->get('archive_order') ? EL()->options->event->get('archive_order') : 'DESC';
			$listing_posts_per_page = EL()->options->event->get('listing_posts_per_page');
			$choose_week_end = EL()->options->general->get('choose_week_end') != null ? EL()->options->general->get('choose_week_end') : array('saturday', 'sunday');

			$keyword = isset( $post_data['keyword'] ) ? sanitize_text_field( $post_data['keyword'] ) : '';
			$cat = isset( $post_data['cat'] ) ? sanitize_text_field( $post_data['cat'] ) : '';
			$sort = isset( $post_data['sort'] ) ? sanitize_text_field( $post_data['sort'] ) : apply_filters( 'search_event_sort_default', 'date-desc' );

			$name_venue = isset( $post_data['name_venue'] ) ? esc_html( $post_data['name_venue'] ) : '' ;
			$time = isset( $post_data['time'] ) ? sanitize_text_field( $post_data['time'] ) : '';
			$start_date = isset( $post_data['start_date'] ) ? sanitize_text_field( $post_data['start_date'] ) : '';
			$end_date = isset( $post_data['end_date'] ) ? sanitize_text_field( $post_data['end_date'] ) : '';

			$event_state = isset( $post_data['event_state'] ) ? esc_html( $post_data['event_state'] ) : '' ;
			$event_city = isset( $post_data['event_city'] ) ? esc_html( $post_data['event_city'] ) : '' ;

			$type = isset( $post_data['type'] ) ? sanitize_text_field( $post_data['type'] ) : '';
			$column = isset( $post_data['column'] ) ? sanitize_text_field( $post_data['column'] ) : '';

			$event_type = isset( $post_data['event_type'] ) ? sanitize_text_field( $post_data['event_type'] ) : '';

			$el_data_taxonomy_custom = isset( $post_data['el_data_taxonomy_custom'] ) ? sanitize_text_field( $post_data['el_data_taxonomy_custom'] )  : '';

			$el_data_taxonomy_custom = str_replace( '\\', '',  $el_data_taxonomy_custom);
			if( $el_data_taxonomy_custom ){
				$el_data_taxonomy_custom = json_decode($el_data_taxonomy_custom, true);

			}

			$max_price = isset( $post_data['max_price'] ) ? sanitize_text_field( $post_data['max_price'] ) : '';
			$min_price = isset( $post_data['min_price'] ) ? sanitize_text_field( $post_data['min_price'] ) : '';

			$paged = isset( $post_data['paged'] ) ? (int)$post_data['paged']  : 1;

			$filter_events = EL()->options->event->get('filter_events', 'all');
			$current_time = current_time('timestamp');

			$args_base = array(
				'post_type'      => 'event',
				'post_status'    => 'publish',
				'paged'          => $paged,
				'posts_per_page' => $listing_posts_per_page,
			);

			$args_order =  array( 'order' => 'DESC' );

			switch ( $sort ) {
				// Settings Order
				case '':

				switch ( $orderby ) {
					case 'title':
					$args_orderby =  array( 'orderby' => 'title' );
					$args_order =  array( 'order' => $order );
					break;

					case 'start_date':
					$args_orderby =  array( 'orderby' => 'meta_value_num', 'meta_key' => $_prefix.'start_date_str' );
					$args_order =  array( 'order' => $order );
					break;

					case 'ID':
					$args_orderby =  array( 'orderby' => 'ID');
					$args_order =  array( 'order' => $order );
					break;

					case 'end_date':
					$args_orderby =  array( 'orderby' => 'meta_value_num', 'meta_key' => $_prefix.'start_date_str' );
					$args_order =  array( 'order' => $order );
					break;

					case 'near':
					$args_orderby = array( 'orderby' => 'post__in');
					$args_order = array( 'order' => 'ASC' );
					break;

					case 'date_desc':
					$args_orderby =  array( 'orderby' => 'date' );
					break;

					case 'date_asc':
					$args_orderby = array( 'orderby' => 'date' );
					$args_order = array( 'order' => 'ASC' );
					break;
					
					default:
					break;
				}
				
				break;
				// Filter Order
				case 'date-desc':
				$args_orderby =  array( 'orderby' => 'date' );
				break;

				case 'date-asc':
				$args_orderby = array( 'orderby' => 'date' );
				$args_order = array( 'order' => 'ASC' );
				break;

				case 'near':
				$args_orderby = array( 'orderby' => 'post__in');
				$args_order = array( 'order' => 'ASC' );
				break;

				case 'start-date':
				$args_orderby = array( 'orderby' => 'meta_value_num', 'meta_key' => $_prefix.'start_date_str' );
				$args_order = array( 'order' => 'ASC' );
				break;

				case 'end-date':
				$args_orderby = array( 'orderby' => 'meta_value_num', 'meta_key' => $_prefix.'end_date_str' );
				$args_order = array( 'order' => 'ASC' );
				break;

				case 'a-z':
				$args_orderby = array( 'orderby' => 'title');
				$args_order = array( 'order' => 'ASC' );
				break;

				case 'z-a':
				$args_orderby = array( 'orderby' => 'title');
				break;

				default:
				break;
			}

			$args_basic = array_merge_recursive( $args_base, $args_order, $args_orderby,$args_featured );

			$args_filter_price = array();
			if ( $max_price != '' && $min_price != '' ) {
				$decimals = (int) EL()->options->general->get('number_decimals',2);

				$max_price_format = (float) $max_price;
				$max_price_format = round($max_price_format,$decimals);

				$min_price_format = (float) $min_price;
				$min_price_format = round($min_price_format,$decimals);

				$args_filter_price = array(
					'meta_query' => array(
						array(
							'relation' => 'OR',
							array(
								'key' => OVA_METABOX_EVENT.'min_price',
								'value' => array($min_price_format, $max_price_format),
								'compare' => 'BETWEEN',
								'type' => 'DECIMAL',
							),
							array(
								'key' => OVA_METABOX_EVENT.'max_price',
								'value' => array($min_price_format, $max_price_format),
								'compare' => 'BETWEEN',
								'type' => 'DECIMAL',
							),
						),
					),
				);
			}

			$args_radius = $args_name = $args_cat = $args_time = $args_date = $args_venue = $args_state = $args_city = $args_event_type = $args_filter_events = array();

			// Query Result
			if ( $results ) {
				$args_radius = array( 'post__in' => $results );
			}

			// Query Keyword
			if( $keyword ){
				$args_name = array( 's' => $keyword );
			}

			// Query Categories
			if($cat){
				$args_cat = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'event_cat',
							'field'    => 'slug',
							'terms' => $cat
						)
					)
				);
			}


			//Query Custom Taxonomy
			$arg_taxonomy_arr = [];
			if( $el_data_taxonomy_custom ) {
				$arg_taxonomy_arr = [];
			    if ( ! empty( $el_data_taxonomy_custom ) ) {
			        foreach( $el_data_taxonomy_custom as $taxo => $value_taxo ) {
			        	if( ! empty( $value_taxo ) ) {
			        		$arg_taxonomy_arr[] = array(
		                		'taxonomy' => $taxo,
			                    'field' => 'slug',
			                    'terms' => $value_taxo
			                );
			        	}
			        }
			    }

			    if( !empty($arg_taxonomy_arr) ){
			        $arg_taxonomy_arr = array(
			            'tax_query' => $arg_taxonomy_arr
			        );
			    }
			}


			// Query Time
			if($time){

				$date_format = 'Y-m-d 00:00';
				$today_day = current_time( $date_format);

				// Return number of current day
				$num_day_current = date('w', strtotime( $today_day ) );

				// Check start of week in wordpress
				$start_of_week = get_option('start_of_week');

				// This week
				$week_start = date( 'Y-m-d', strtotime($today_day) - ( ($num_day_current - $start_of_week) *24*60*60) );
				$week_end = date( 'Y-m-d', strtotime($today_day)+ (7 - $num_day_current + $start_of_week )*24*60*60 );
				$this_week = el_getDatesFromRange( $week_start, $week_end );
				$this_week_regexp = implode( '|', $this_week );
				

				// Get Saturday in this week
				$saturday = strtotime( date($date_format, strtotime('this Saturday')));
				// Get Sunday in this week
				$sunday = strtotime( date( $date_format, strtotime('this Sunday')));
				// Weekend
				$week_end = el_getDatesFromRange( date( 'Y-m-d', $saturday ), date( 'Y-m-d', $sunday ) );
				$week_end_regexp = implode('|', $week_end );
				


				// Next week Start
				$next_week_start = strtotime($today_day)+ (7 - $num_day_current + $start_of_week )*24*60*60;
				// Next week End
				$next_week_end = $next_week_start+7*24*60*60;
				
				// Next week
				$next_week = el_getDatesFromRange( date( 'Y-m-d', $next_week_start ), date( 'Y-m-d', $next_week_end ) );
				$next_week_regexp = implode( '|', $next_week );
				

				// Month Current
				$num_day_current = date('n', strtotime( $today_day ) );

				// First day of next month
				$first_day_next_month = strtotime( date( $date_format, strtotime('first day of next month') ) );
				$last_day_next_month = strtotime ( date( $date_format, strtotime('last day of next month') ) )+24*60*60+1;
				// Next month
				$next_month = el_getDatesFromRange( date( 'Y-m-d', $first_day_next_month ), date( 'Y-m-d', $last_day_next_month ) );
				$next_month_regexp = implode( '|', $next_month );
				
				
				

				switch ($time) {
					case 'today':
					$args_time = array(
						'meta_query' => array(
							array(
								'key' => $_prefix.'event_days',
								'value' => strtotime($today_day),
								'compare' => 'LIKE'	
							),
						)
					);

					break;

					case 'tomorrow':
					$args_time = array(
						'meta_query' => array(
							array(
								'key' => $_prefix.'event_days',
								'value' => strtotime($today_day) + 24*60*60,
								'compare' => 'LIKE'	
							),
						)
					);
					break;

					case 'this_week':
					$args_time = array(
						'meta_query' => array(
							array(
								'key' => $_prefix.'event_days',
								'value' => $this_week_regexp,
								'compare' => 'REGEXP'	
							),
						)
					);
					break;

					case 'this_week_end':
					$args_time = array(
						'meta_query' => array(
							array(
								'key' => $_prefix.'event_days',
								'value' => $week_end_regexp,
								'compare' => 'REGEXP'	
							),
						)
					);
					break;

					case 'next_week':
					$args_time = array(
						'meta_query' => array(
							array(
								'key' => $_prefix.'event_days',
								'value' => $next_week_regexp,
								'compare' => 'REGEXP'	
							),
						)
					);
					break;

					case 'next_month':
					$args_time = array(
						'meta_query' => array(
							array(
								'key' => $_prefix.'event_days',
								'value' => $next_month_regexp,
								'compare' => 'REGEXP'	
							),
						)
					);
					break;

					default:
						# code...
					break;
				}
			}

			// Query Date
			if( $start_date && $end_date ){

				$between_dates = el_getDatesFromRange( date( 'Y-m-d', strtotime( $start_date ) ), date( 'Y-m-d', strtotime( $end_date ) + 24*60*60 ) );
				$between_dates_regexp = implode( '|', $between_dates );

				$args_date = array(
					'meta_query' => array(
						array(
							'key' => $_prefix.'event_days',
							'value' => $between_dates_regexp,
							'compare' => 'REGEXP'
						),
					)
				);

			}else if( $start_date && ! $end_date ){

				$args_date = array(
					'meta_query' => array(
						array(
							'key' => $_prefix.'event_days',
							'value' => strtotime( $start_date ),
							'compare' => 'LIKE'
						)
					)	
				);

			} else if( ! $start_date && $end_date ){
				$args_date = array(
					'meta_query' => array(
						array(
							'key' => $_prefix.'event_days',
							'value' => strtotime( $end_date ),
							'compare' => 'LIKE'
						),
					)	
				);
			}

			// Query Venue
			if($name_venue){
				$args_venue = array(
					'meta_query' => array(
						array(
							'key' => $_prefix.'venue',
							'value' => $name_venue,
							'compare' => 'LIKE'
						)
					)
				);
			}

			// Query State
			if($event_state){
				$args_state = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'event_loc',
							'field'    => 'slug',
							'terms' => $event_state
						)
					)
				);
			}

			// Query City
			if($event_city){
				$args_city = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'event_loc',
							'field'    => 'slug',
							'terms' => $event_city
						)
					)
				);
			}

			// Query Event Type
			if( $event_type ){
				$args_event_type = array(
					'meta_query' => array(
						array(
							'key' => $_prefix.'event_type',
							'value' => $event_type,
							'compare' => 'LIKE'	
						),
					)
				);
			}

			

			// Query filter
			$args_filter_events = el_sql_filter_status_event( $filter_events );

			$args = array_merge_recursive( $args_basic, $args_radius, $args_name, $args_cat, $args_time , $args_date, $args_venue, $args_state, $args_city, $args_event_type, $args_filter_events, $arg_taxonomy_arr, $args_filter_price );
			
			$events = new WP_Query( apply_filters( 'el_search_map_event_query', $args, $post_data  ) );

			/***** End Query Post in Radius *****/
			
			ob_start();
			
			?>

			<div class="event_archive <?php echo esc_attr($type . ' ' . $column); ?>" style="display: grid;">
				<?php
				if($events->have_posts() ) : while ( $events->have_posts() ) : $events->the_post();

					el_get_template_part( 'content', 'event-'.$type );
					$id = get_the_id();

					$lat_event = get_post_meta( $id, OVA_METABOX_EVENT.'map_lat', true );
					$lng_event = get_post_meta( $id, OVA_METABOX_EVENT.'map_lng', true );
					
					?>
					<div class="data_event" style="display: none;"
					data-link_event="<?php echo esc_attr( get_the_permalink() ); ?>"
					data-title_event="<?php echo esc_attr( get_the_title() ); ?>"
					data-date="<?php echo get_event_date_el('simple'); ?>"
					data-average_rating="<?php echo get_average_rating_by_id_event( get_the_id() ); ?>"
					data-number_comment="<?php echo get_number_coment_by_id_event( get_the_id() ); ?>"
					

					data-map_lat_event="<?php echo $lat_event; ?>"
					data-map_lng_event="<?php echo $lng_event; ?>"

					data-thumbnail_event="<?php echo esc_attr( ( has_post_thumbnail() && get_the_post_thumbnail() ) ? wp_get_attachment_image_url( get_post_thumbnail_id() , 'el_img_squa' ) : EL_PLUGIN_URI.'assets/img/no_tmb_square.png' ); ?>"
					data-marker_price="<?php echo esc_attr(get_price_ticket_by_id_event( $id )); ?>"
					data-marker_date="<?php echo esc_attr(get_event_date_el('map_simple')); ?>"
					 data-show_featured="<?php echo esc_attr($show_featured); ?>"
					></div>

				<?php endwhile; wp_reset_postdata(); else: ?>

				<div class="not_found_event"> <?php esc_html_e( 'Not found event', 'eventlist' ); ?> </div>

				<?php ; endif; ?>
			</div>

			<?php 
			$total = $events->max_num_pages;

			if ( $total > 1 ) {  ?>
				<div class="el-pagination">
					<?php 
					el_pagination_event_ajax($events->found_posts, $events->query_vars['posts_per_page'], $paged);
					?>
				</div>
			<?php }
			$result = ob_get_contents(); 
			ob_end_clean();

			ob_start(); ?>
			<div class="listing_found">
				<?php if ($events->found_posts == 1) { ?>
					<span><?php echo sprintf( esc_html__( '%s Result Found', 'eventlist' ), esc_html( $events->found_posts ) ); ?></span>
				<?php } else { ?>
					<span><?php echo sprintf( esc_html__( '%s Results Found', 'eventlist' ), esc_html( $events->found_posts ) ); ?></span>
				<?php } ?>

				<?php if ( $paged == ceil($events->found_posts/$events->query_vars['posts_per_page']) ) { ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing %s-%s)', 'eventlist' ), esc_html( (($paged - 1) * $events->query_vars['posts_per_page'] + 1)), esc_html($events->found_posts) ); ?>
					</span>
				<?php } elseif( !$events->have_posts() ) { ?>
					<span></span>
				<?php } else { ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing %s-%s)', 'eventlist' ), esc_html(($paged - 1) * $events->query_vars['posts_per_page'] + 1), esc_html($paged * $events->query_vars['posts_per_page']) ); ?>
					</span>
				<?php } ?>
			</div>

			<?php
			$pagination = ob_get_contents();
			ob_end_clean();

			echo json_encode(array("result"=>$result, "pagination"=>$pagination));

			wp_die();
		}

		public function el_filter_elementor_grid () {

			if( !isset( $_POST ) ) wp_die();

			$filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : "";
			$status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : "";
			$order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : "";
			$orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : "";
			$number_post = isset($_POST['number_post']) ? sanitize_text_field($_POST['number_post']) : "";
			$term_id_filter_string = isset($_POST['term_id_filter_string']) ? sanitize_text_field($_POST['term_id_filter_string']) : "";
			$type_event = isset($_POST['type_event']) ? sanitize_text_field($_POST['type_event']) : "";

			$term_id_filter = explode(',', $term_id_filter_string);

			$current_time = current_time('timestamp');

			$agrs_base = [
				'post_type' => 'event',
				'post_status' => 'publish',
				'posts_per_page' => $number_post,
				'order' => $order,
				// 'orderby' => $orderby,
			];


			switch ($orderby) {
				case 'date':
				$args_orderby =  array( 'orderby' => 'date' );

				break;
				case 'title':
				$args_orderby =  array( 'orderby' => 'title' );
				break;

				case 'start_date':
				$args_orderby =  array( 'orderby' => 'meta_value_num', 'meta_key' => OVA_METABOX_EVENT.'start_date_str' );
				break;

				case 'id':
				$args_orderby =  array( 'orderby' => 'ID');
				break;

				default:
				break;
			}

			if ($filter == 'all') {
				$agrs_filter = [
					'tax_query' => [
						[
							'taxonomy' => 'event_cat',
							'field'    => 'id',
							'terms'    => $term_id_filter,
						]
					]
				];
			} else {
				$agrs_filter = [
					'tax_query' => [
						[
							'taxonomy' => 'event_cat',
							'field'    => 'id',
							'terms'    => $filter,
						]
					]
				];
			}

			switch ( $status ) {
				case 'feature' : {
					$agrs_status = [
						'meta_query' => [
							[
								'key' => OVA_METABOX_EVENT . 'event_feature',
								'value' => 'yes',
								'compare' => '=',
							],
						],
					];
					break;
				}
				case 'upcoming' : {
					$agrs_status = [
						'meta_query' => 
						[
							'relation' => 'AND',
							[
								'key' => OVA_METABOX_EVENT . 'end_date_str',
								'value' => $current_time,
								'compare' => '>',
								'type'	=> 'NUMERIC'
							],
							[
								'relation' => 'OR',
								[
									'key' => OVA_METABOX_EVENT . 'start_date_str',
									'value' => $current_time,
									'compare' => '>',
									'type'	=> 'NUMERIC'
								],
								[
									'key' => OVA_METABOX_EVENT . 'option_calendar',
									'value' => 'auto',
									'compare' => '='
								],
							]

						]
					];
					break;
				}
				case 'selling' : {
					$agrs_status = [
						'meta_query' => [
							'relation' => 'AND',
							[
								'key' => OVA_METABOX_EVENT . 'start_date_str',
								'value' => $current_time,
								'compare' => '<=',
							],
							[
								'key' => OVA_METABOX_EVENT . 'end_date_str',
								'value' => $current_time,
								'compare' => '>='
							]
						],
					];
					break;
				}

				case 'upcoming_selling': {
					$agrs_status = [
						'meta_query' => [
							'key'      => OVA_METABOX_EVENT . 'end_date_str',
							'value'    => $current_time,
							'compare'  => '>'
						],
					];
					break;
				}

				case 'closed' : {
					$agrs_status = [
						'meta_query' => [
							[
								'key' => OVA_METABOX_EVENT . 'end_date_str',
								'value' => $current_time,
								'compare' => '<',
							]
						],
					];
					break;
				}

				default : {
					$agrs_status = [];
				}
			}

			$agrs = array_merge($agrs_base, $agrs_filter, $agrs_status, $args_orderby);

			$events = new WP_Query( $agrs );
			?>
			<div class="wrap_loader">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#a1a1a1"/>
					<circle cx="25" cy="25" r="20" stroke="#a1a1a1"/>
				</svg>
			</div>

			<?php
			if($events->have_posts()) : 
				while($events->have_posts()) : $events->the_post();
					el_get_template_part( 'content', 'event-'.$type_event );
				endwhile; wp_reset_postdata(); 
			else:
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php 
			endif;

			wp_die();
		}

		public function el_single_send_mail_report() {

			if( ! is_user_logged_in() ) {
				echo 'false';
				wp_die();
			} 

			$data = $_POST['data'];

			$id_event = (isset($data['id_event'])) ? sanitize_text_field($data['id_event']) : wp_die();
			$message = (isset($data['message'])) ? sanitize_text_field($data['message']) : "";
			
			$name_event = get_the_title($id_event);
			$link_event = get_the_permalink($id_event);

			$subject = EL()->options->mail->get( 'mail_report_event_subject', esc_html__( 'Report event', 'eventlist' ) );

			$body = EL()->options->mail->get('mail_report_event_content');

			if( !$body ) $body = 'The link event: [el_link_event]. [el_message]';

			$body = str_replace( '&lt;br&gt;', "<br>", $body );
			$body = str_replace( '[el_link_event]', '<a href="'.$link_event.'">'. $name_event . '</a><br>', $body);
			$body = str_replace( '[el_message]', esc_html( $message ) . "<br>", $body);

			if(el_submit_sendmail_report( $id_event, $subject, $body)) {
				echo 'true';
			} else {
				echo 'false';
			}

			wp_die();
		}

		public function el_single_send_mail_vendor() {
			
			if(!isset($_POST['data'])) wp_die();

			$data = $_POST['data'];

			$id_event = (isset($data['id_event'])) ? sanitize_text_field($data['id_event']) : wp_die();
			$name_event = get_the_title($id_event);
			$permalink = get_permalink( $id_event );

			$name = (isset($data['name'])) ? sanitize_text_field($data['name']) : "";
			$email = (isset($data['email'])) ? sanitize_email($data['email']) : "";
			$phone = (isset($data['phone'])) ? sanitize_text_field($data['phone']) : "";
			$subject = (isset($data['subject'])) ? sanitize_text_field($data['subject']) : esc_html__( 'The guest contact ', 'eventlist' ).$name_event;
			$content = (isset($data['content'])) ? sanitize_text_field($data['content']) : "[el_content]";
			

			$body = EL()->options->mail->get('mail_vendor_email_template');
			if( !$body ){
				$body = 'Event: [el_event_name]<br/>Name: [el_name]<br/>Email: [el_mail]<br/>Phone: [el_phone]<br/>Email: [el_content]';
			}
			$body = str_replace( '&lt;br&gt;', "<br>", $body );
			$body = str_replace( '[el_event_name]', '<a href="'.$permalink.'">'.esc_html( $name_event ).'</a>'. '<br>', $body );
			$body = str_replace( '[el_name]', esc_html( $name ) . '<br>', $body);
			$body = str_replace( '[el_mail]',esc_html( $email ) . '<br>', $body);
			$body = str_replace( '[el_phone]',esc_html( $phone ) . '<br>', $body);
			$body = str_replace( '[el_content]',esc_html( $content ) . '<br>', $body);

			if(el_custom_send_mail_vendor( $email, $id_event, $subject, $body)) {
				echo 'true';
			} else {
				echo 'false';
			}

			wp_die();
		}

		/* Change password */
		public static function el_update_role() {

			if( !apply_filters( 'el_is_update_vendor_role', true ) ) wp_die();
			if( !isset( $_POST['data'] ) ) wp_die();

			$post_data = $_POST['data'];
			
			if( !isset( $post_data['el_update_role_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_update_role_nonce'] ), 'el_update_role_nonce' ) ) return ;

			$role = isset( $post_data['role'] ) ? sanitize_text_field( $post_data['role'] ) : '';
		
			$user_id = wp_get_current_user()->ID;

			if ( $role == 'vendor' ) {
				
				$user = new WP_User( $user_id );
				$user->set_role( 'el_event_manager' );
				$member_account_id = EL()->options->general->get( 'myaccount_page_id', '' );
				$redirect_page = get_the_permalink( $member_account_id );

				$enable_package = EL()->options->package->get( 'enable_package', 'yes' );
				$default_package = EL()->options->package->get( 'package' );
				
				if( $enable_package == 'yes' && $default_package ){

					$pid = EL_Package::instance()->get_package( $default_package );

					if( EL_Package::instance()->add_membership( $pid['id'], $user_id, $status = 'new' ) ){
						$redirect_page = add_query_arg( 'vendor', 'package', $redirect_page );		
					}
					
				}
				
				echo $redirect_page;
				wp_die();

			} 

			wp_die();
			
		}

		public function el_check_vendor_field_required(){
			$post_data = $_POST;

			$nonce = isset( $post_data['nonce'] ) ? sanitize_text_field( $post_data['nonce'] ) : '';
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;

			$response = [];
			$response['status'] = 'error';
			$response['mess'] = '';
			$response['show_vendor_field'] = 'no';
			$response['vendor_field'] = '';
			$response['send_mail'] = '';
			$response['mail_mess'] = '';
			$response['reload_page'] = 'no';

			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'el_update_role_nonce' ) ) {
				$response['mess'] = esc_html__( 'Nonce is invalid', 'eventlist' );
				echo json_encode( $response );
				wp_die();
			}

			// check vendor field is not empty
			$user_meta_field = get_option( 'ova_register_form' );

			$flag = true;
			if ( ! empty( $user_meta_field ) && is_array( $user_meta_field ) ) {
				foreach ( $user_meta_field as $name => $field ) {
					if ( $field['used_for'] !== 'user' && $field['required'] == 'on' && $field['enabled'] == "on" ) {
						$user_meta_val = get_user_meta( $user_id, 'ova_'.$name, true );
						if ( empty( $user_meta_val ) ) {
							$flag = false;
						}
					}
				}
			}

			$vendor_status = get_user_meta( $user_id, 'vendor_status', true );

			switch ( $vendor_status ) {

				case 'pending':
					$response['mess'] = esc_html__( 'Your request is being approved by the administrator.', 'eventlist' );
					echo json_encode( $response );
					wp_die();
				break;

				case 'approve':
					$flag = false;
					$response['reload_page'] = 'yes';
				break;

				case 'reject':
					$flag = false;
				break;

				default:
					break;
			}

			if ( $flag ) {
				$current_time = current_time( 'timestamp' );
				update_user_meta( $user_id, 'vendor_status', 'pending');
				update_user_meta( $user_id, 'update_vendor_time', $current_time);
				// send mail to admin
				$user_email = $current_user->user_email;

				if ( ! ova_register_vendor_mailto_admin( $user_email ) ) {
					$response['send_mail'] = 'error';
					$response['mail_mess'] = esc_html__( 'An error occurred while sending notification email to the administrator.', 'eventlist' );
				}

				$response['status'] = 'success';
				$response['mess'] = esc_html__( 'You have successfully submitted your request, please wait for the administrator to approve.', 'eventlist' );
			} else {
				$response['mess'] = esc_html__( 'Please fill in all vendor information below and update your profile, then click the upgrade to Vendor Role button again.', 'eventlist' );
				$response['show_vendor_field'] = 'yes';
				$response['vendor_field'] = el_get_profile_custom_field_vendor( $user_id );
			}

			echo json_encode( $response );
			wp_die();
		}

		// Cancel Booking
		public static function el_cancel_booking(){

			if(!isset( $_POST )) wp_die();
			
			$id_booking = isset( $_POST['id_booking'] ) ? $_POST['id_booking'] : '';

			if( !isset( $_POST['el_cancel_booking_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['el_cancel_booking_nonce'] ), 'el_cancel_booking_nonce' ) ) return ;

			if( $id_booking && el_cancellation_booking_valid( $_POST['id_booking'] ) ){

				$id_customer_booking = get_post_meta( $id_booking, OVA_METABOX_EVENT.'id_customer', true );
				$current_user_id = get_current_user_id();

				// Check exactly customer who buy event
				if( $current_user_id == $id_customer_booking || current_user_can( 'administrator' ) ){
					
						$booking_update = array(
							'ID'           => $id_booking,
							'post_date'		=> current_time('mysql'),
							'meta_input'	=> array(
								OVA_METABOX_EVENT.'status' => 'Canceled',
							)
						);

						if( wp_update_post( $booking_update ) ){

							do_action( 'el_cancel_booking_succesfully', $id_booking );
							do_action( 'el_update_ticket_rest_cancel_booking_succesfully', $id_booking );
							echo json_encode( array( 'status' => 'success', 'msg' => esc_html__( 'Cancel Sucessfully', 'eventlist' ) ) );
							wp_die();
						}

				}
				
			}

			echo json_encode( array( 'status' => 'error', 'msg' => esc_html__( 'Error Cancellation', 'eventlist' ) ) );
			wp_die();
		}

        //load edit ticket in manage ticket
		public static function el_load_edit_ticket_calendar() {



				/**
				* Hook: el_vendor_edit_manage_ticket_max - 10
		        * @hooked:  el_vendor_edit_manage_ticket_max- 10
				*/
				do_action( 'el_vendor_edit_manage_ticket_max' );



		

			wp_die();



		}


		//choose_calendar in manage ticket


		public static function el_choose_calendar() {


				/**
				* Hook: el_vendor_calendar_manage_ticket - 10
		        * @hooked:  el_vendor_calendar_manage_ticket- 10
				*/
				do_action( 'el_vendor_calendar_manage_ticket' );



			wp_die();



		}


		/* 	update ticket max */
		public static function 	el_update_ticket_max() {

			if( !isset( $_POST['data'] ) ) wp_die();

			$_prefix = OVA_METABOX_EVENT;

			$post_data = $_POST['data'];

			if( !isset( $post_data['el_update_ticket_max_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $post_data['el_update_ticket_max_nonce'] ), 'el_update_ticket_max_nonce' ) ) return ;

			$cal_id  = isset( $post_data['cal_id'] ) ? sanitize_text_field( $post_data['cal_id'] ) : '';
			$id = isset( $post_data['id'] ) ? sanitize_text_field( $post_data['id'] ) : '';
			$ticket_max = isset( $post_data['ticket_max'] ) ? ( $post_data['ticket_max'] ) : '';
			$max_ticket = get_post_meta( $id,  $_prefix.'ticket_max['.$cal_id.'_'. $value['ticket_id'].']', true);
            
			foreach ( $ticket_max as  $value ) {
				$number_ticket_sold = EL_Booking::instance()->get_number_ticket_booked($id, $cal_id,  $value['ticket_id']);
				if($number_ticket_sold > $value['ticket_max']  ){

					echo json_encode( array(  'msg' => esc_html__( 'Number ticket max must more than ', 'eventlist' ).' '.floatval( $number_ticket_sold ) ) );
					wp_die();

				}
				if(isset($max_ticket)){
					update_post_meta( $id,  $_prefix.'ticket_max['.$cal_id.'_'. $value['ticket_id'].']', $value['ticket_max'] );
				}else{

					add_post_meta( $id,  $_prefix.'ticket_max['.$cal_id.'_'. $value['ticket_id'].']', $value['ticket_max'] );

				}


			}
			

			echo json_encode( array( 'message' =>  esc_html__( 'Updated success!', 'eventlist' ) ) );


			
			wp_die();
		}




         
		public function el_check_date_search_ticket() {
			$start_time = isset($_POST['start_time']) ? sanitize_text_field($_POST['start_time']) : '';
			$end_time = isset($_POST['end_time']) ? sanitize_text_field($_POST['end_time']) : '';
			$eid = isset($_POST['eid']) ? sanitize_text_field($_POST['eid']) : '';

			$start = isset( $start_time ) ? el_get_time_int_by_date_and_hour( $start_time,0) : '';
			$end = isset( $end_time ) ? el_get_time_int_by_date_and_hour( $end_time,0) : '';

			$check_number = floatval($end - $start);
			$number_day = EL()->options->role->get('day_search_ticket') ? EL()->options->role->get('day_search_ticket') : '7';
			$check_time = floatval($number_day)*24*60*60; 


			if($check_number > $check_time){

				echo json_encode( array(  'msg' => esc_html__( 'Number of search days must be less', 'eventlist' ).' '.floatval( $number_day ) ) );
				wp_die();

			}

			$member_account_id = EL()->options->general->get( 'myaccount_page_id', '' );
			$redirect_page = get_the_permalink( $member_account_id );
			$redirect_page = add_query_arg( 'vendor', 'manage_event&eid='.$eid.'&start_date_2='.$start_time.'&end_date_2='.$end_time, $redirect_page );

			echo json_encode( array(  'url' => $redirect_page ));	
			wp_die();
		}

		public function el_multiple_customers_ticket() {

			if ( ! isset($_POST['data']) ) return false;
			if ( ! isset( $_POST['data']['el_next_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field($_POST['data']['el_next_event_nonce']), 'el_next_event_nonce' ) ) return false;

			$post_data 		= $_POST['data'];
			$event_id 		= isset( $post_data['event_id'] ) ? $post_data['event_id'] : '';
			$seat_option 	= isset( $post_data['seat_option'] ) ? sanitize_text_field( $post_data['seat_option'] ) : 'no_seat';

			$cart 	= isset($post_data['cart']) ? $post_data['cart'] : array();
			$nav 	= $result = $ticket_type = $seat_map = '';
			
			if ( ! empty( $cart ) && is_array( $cart ) ) {
				$qty = 0;
				$ticket_ids = array();
				$seat_names = [];

				if ( 'map' === $seat_option ) {
					foreach ( $cart as $cart_item ) {
						if ( isset( $cart_item['qty'] ) && absint( $cart_item['qty'] ) ) {
							$qty += absint( $cart_item['qty'] );
							$seat_names[] = $cart_item['id'];
						} else if( isset( $cart_item['data_person'] ) ){
							foreach ($cart_item['data_person'] as $k => $val) {
								$qty += (int) $val['qty'];
								for ($i=0; $i < (int) $val['qty']; $i++) { 
									$seat_names[] = $cart_item['id'].' - '.$val['name'];
								}
								
							}
						} else {
							$qty += 1;
							$seat_names[] = $cart_item['id'];
						}
					}
					// seat map for first ticket form
					$seat_map 	= el_get_seat_html_form_cart( $seat_names, $index = 0 );
					$ticket_ids = el_get_ticket_ids_form_cart( $cart, 'map' );
				} else {
					$qty 			= el_get_quantity_form_cart( $cart );
					$ticket_type 	= el_get_ticket_type_html_form_cart( $cart );
					$ticket_ids 	= el_get_ticket_ids_form_cart( $cart );
				}

				// Quantity HTML
				if ( $qty && $qty > 1 ) {
					ob_start();
					?>
						<ul class="el_multiple_ticket">
					<?php
						for ( $i = 0; $i < $qty; $i++ ): 
							$active = '';

							if ( $i === 0 ) {
								$active = ' actived';
							}

							$ticket_id = isset( $ticket_ids[$i] ) && $ticket_ids[$i] ? $ticket_ids[$i] : '';
						?>
							<li 
								class="ticket_item ticket_item_<?php esc_attr_e( $i ); ?><?php esc_attr_e( $active ); ?>" 
								data-index="<?php esc_attr_e( $i ); ?>" 
								data-ticket-id="<?php esc_attr_e( $ticket_id ); ?>">
								<?php printf( esc_html__( 'Ticket #%s', 'eventlist' ), $i + 1 ); ?>
							</li>
					<?php endfor; ?>
						</ul>
					<?php
					$nav = ob_get_contents();
					ob_end_clean();

					// Result
					ob_start();
					el_get_template( 'cart/customer_insert.php', $post_data );
					$result = ob_get_contents();
					ob_end_clean();
				}
			}

			if ( $nav && ( $ticket_type || $seat_map ) ) {
				echo json_encode( array(
					"nav" 			=> $nav,
					"result" 		=> $result,
					"ticket_type" 	=> $ticket_type,
					"seat_map" 		=> $seat_map,
				));
			} else {
				echo 0;
			}

			wp_die();
		}

		public function el_geocode(){
			$ids 			= isset( $_POST['data']['ids'] ) ? $_POST['data']['ids'] : 0;
			$cate_id 		= isset( $_POST['data']['cate_id'] ) ? sanitize_text_field( $_POST['data']['cate_id'] ) : 0;
			$status 		= isset( $_POST['data']['status'] ) ? sanitize_text_field( $_POST['data']['status'] ) : '';
			$posts_per_page = sanitize_text_field( $_POST['data']['query']['posts_per_page'] );
			$order 			= sanitize_text_field( $_POST['data']['query']['order'] );
			$orderby 		= sanitize_text_field( $_POST['data']['query']['orderby'] );
			$type_event 	= sanitize_text_field( $_POST['data']['type'] );
			$type_event 	= !empty($type_event) ? $type_event : 'type1';
			$events 		= get_list_event_near_by_id( $order, $orderby, $posts_per_page, $ids, $cate_id, $status );

			?>
			<div class="wrap_loader">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#a1a1a1"/>
					<circle cx="25" cy="25" r="20" stroke="#a1a1a1"/>
				</svg>
			</div>
			<?php
			if( $events && $events->have_posts() ) : 
				while($events->have_posts()) : $events->the_post();
					el_get_template_part( 'content', 'event-'.$type_event );
				endwhile; wp_reset_postdata();
			else :
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php 
			endif;
			wp_die();
		}

		public function el_event_default(){
			$posts_per_page = sanitize_text_field( $_POST['data']['query']['posts_per_page'] );
			$order 			= sanitize_text_field( $_POST['data']['query']['order'] );
			$orderby 		= sanitize_text_field( $_POST['data']['query']['orderby'] );
			$filter_event 	= sanitize_text_field( $_POST['data']['query']['filter_event'] );
			$type_event 	= sanitize_text_field( $_POST['data']['type'] );
			$type_event 	= !empty($type_event) ? $type_event : 'type1';
			$events 		= get_list_event_near_elementor($order, $orderby, $posts_per_page, $filter_event);
			?>
			<div class="wrap_loader">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#a1a1a1"/>
					<circle cx="25" cy="25" r="20" stroke="#a1a1a1"/>
				</svg>
			</div>
			<?php
			if( $events && $events->have_posts() ) : 
				while($events->have_posts()) : $events->the_post();
					el_get_template_part( 'content', 'event-'.$type_event );
				endwhile; wp_reset_postdata();
			else :
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php 
			endif;
			wp_die();
		}

		public function el_event_online(){
			$posts_per_page = sanitize_text_field( $_POST['data']['query']['posts_per_page'] );
			$order 			= sanitize_text_field( $_POST['data']['query']['order'] );
			$orderby 		= sanitize_text_field( $_POST['data']['query']['orderby'] );
			$filter_event 	= sanitize_text_field( $_POST['data']['query']['filter_event'] );
			$type_event 	= sanitize_text_field( $_POST['data']['type'] );
			$type_event 	= !empty($type_event) ? $type_event : 'type1';
			$events 		= get_list_event_near_elementor($order, $orderby, $posts_per_page, $filter_event, 'online');
			?>
			<div class="wrap_loader">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#a1a1a1"/>
					<circle cx="25" cy="25" r="20" stroke="#a1a1a1"/>
				</svg>
			</div>
			<?php
			if( $events && $events->have_posts() ) : 
				while($events->have_posts()) : $events->the_post();
					el_get_template_part( 'content', 'event-'.$type_event );
				endwhile; wp_reset_postdata();
			else :
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php 
			endif;
			wp_die();
		}

		public function el_event_by_time(){
			$ids 			= isset( $_POST['data']['ids'] ) ? $_POST['data']['ids'] : 0;
			$status 		= isset( $_POST['data']['status'] ) ? sanitize_text_field( $_POST['data']['status'] ) : '';
			$posts_per_page = sanitize_text_field( $_POST['data']['query']['posts_per_page'] );
			$order 			= sanitize_text_field( $_POST['data']['query']['order'] );
			$orderby 		= sanitize_text_field( $_POST['data']['query']['orderby'] );
			$type_event 	= sanitize_text_field( $_POST['data']['type'] );
			$event_time 	= sanitize_text_field( $_POST['data']['time'] );
			$type_event 	= !empty($type_event) ? $type_event : 'type1';
			$events = get_list_event_location_by_time_filter($order, $orderby, $posts_per_page, $event_time ,$ids, $status);
			?>
			<div class="wrap_loader">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#a1a1a1"/>
					<circle cx="25" cy="25" r="20" stroke="#a1a1a1"/>
				</svg>
			</div>
			<?php
			if( $events && $events->have_posts() ) : 
				while($events->have_posts()) : $events->the_post();
					el_get_template_part( 'content', 'event-'.$type_event );
				endwhile; wp_reset_postdata();
			else :
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php 
			endif;
			wp_die();
		}

		public function el_event_recent(){
			$id 			= isset( $_POST['data']['id'] ) ? $_POST['data']['id'] : 0;
			$ova_event_id 	= 0;
			$posts_per_page = sanitize_text_field( $_POST['data']['query']['posts_per_page'] );
			$order 			= sanitize_text_field( $_POST['data']['query']['order'] );
			$orderby 		= sanitize_text_field( $_POST['data']['query']['orderby'] );
			$type_event 	= sanitize_text_field( $_POST['data']['type'] );
			$type_event 	= !empty($type_event) ? $type_event : 'type1';

			if ( ! isset( $_COOKIE['ova_event_id'] ) ) {
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php
				wp_die();
			}
			if ( isset( $_COOKIE['ova_event_id'][$id] ) ) {
				unset( $_COOKIE['ova_event_id'][$id] );
				setcookie( 'ova_event_id['.$id.']', '', -1, '/'); 
				$ova_event_id = array_values( $_COOKIE["ova_event_id"] );
			}

			$events = get_list_event_recent_elementor( $order, $orderby, $posts_per_page , $ova_event_id );
			add_filter( 'el_ft_show_remove_btn', '__return_true' );
			?>
			<div class="wrap_loader">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#a1a1a1"/>
					<circle cx="25" cy="25" r="20" stroke="#a1a1a1"/>
				</svg>
			</div>
			<?php
			if( $events && $events->have_posts() ) : 
				while($events->have_posts()) : $events->the_post();
					el_get_template_part( 'content', 'event-'.$type_event );
				endwhile; wp_reset_postdata();
			else :
				?>
				<h3 class="event-notfound"><?php esc_html_e('Event not found', 'eventlist'); ?></h3>
				<?php 
			endif;
			wp_die();
		}

		public function el_upload_files() {
			if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['security'] ), 'el_checkout_event_nonce' ) ) return;

			$files = [];

			if ( $_FILES && is_array( $_FILES ) ) {
				$overrides = [
                    'test_form' => false,
                    'mimes'     => apply_filters( 'ovabrw_ft_file_mimes', [
                        'jpg'   => 'image/jpeg',
                        'jpeg'  => 'image/pjpeg',
                        'png'   => 'image/png',
                        'pdf'   => 'application/pdf',
                        'doc'   => 'application/msword',
                    ]),
                ];

                require_once( ABSPATH . 'wp-admin/includes/admin.php' );

				foreach ( $_FILES as $k => $file ) {
                    $upload = wp_handle_upload( $file, $overrides );

                    if ( isset( $upload['error'] ) ) { continue; }

                    $files[$k] = array(
                        'name' => basename( $upload['file'] ),
                        'url'  => $upload['url'],
                        'mime' => $upload['type'],
                    );
				}
			}

			echo json_encode( array( 'files' => $files ));

			wp_die();
		}

		public function el_verify_google_recapcha(){

			$post_data 		= $_POST['data'];
			$secret_key 	= isset( $post_data['secret'] ) ? sanitize_text_field( $post_data['secret'] ) : '';
			$recapcha 		= isset( $post_data['response'] ) ? sanitize_text_field( $post_data['response'] ) : '';
			$check_recapcha = ova_event_verify_recapcha( $secret_key, $recapcha );

			echo $check_recapcha;
			wp_die();
		}

		public function el_ticket_received_download(){

			$post_data = $_POST;
			$data = [];
			$data['status'] = 'error';
			$nonce = isset( $post_data['nonce'] ) ? sanitize_text_field( $post_data['nonce'] ) : '';
			$ticket_id = isset( $post_data['id'] ) ? sanitize_text_field( $post_data['id'] ) : '';

			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'el_ticket_received_download_nonce' ) ) {
				$data['mess'] = __( 'Invalid nonce, please refresh your screen and try again.', 'eventlist' );
				$data = json_encode( $data );
				echo $data;
				wp_die();
			} elseif ( ! $ticket_id ) {
				$data['mess'] = __( 'Invalid ticket.', 'eventlist' );
				$data = json_encode( $data );
				echo $data;
				wp_die();
			}

			$arr_upload = wp_upload_dir();
			$base_url_upload = $arr_upload['baseurl'];

			$ticket_pdf = EL_Ticket::instance()->make_pdf_ticket_by_id( $ticket_id );
			$ticket_url = '';

			if ( ! empty( $ticket_pdf ) ) {
				$position = strrpos($ticket_pdf, '/');
				$name = substr($ticket_pdf, $position);
				$ticket_url = $base_url_upload . $name;
			}
			
			$data['status'] = 'success';
			$data['ticket_url'] = $ticket_url;
			$data = json_encode( $data );
			echo $data;

			wp_die();
		}

		public function el_fe_unlink_download_ticket() {
			
			$ticket_pdf = $_POST['data_url'];
			$arr_upload = wp_upload_dir();
			$basedir = $arr_upload['basedir'];

			$ticket_url = '';
			if ( ! empty( $ticket_pdf ) ) {
				$position = strrpos($ticket_pdf, '/');
				$name = substr($ticket_pdf, $position);
				$ticket_url = $basedir . $name;
			}

			if (file_exists($ticket_url)) unlink($ticket_url);
			wp_die();
		}

		public function el_ticket_list(){

			$post_data = $_POST;

			if ( ! isset( $post_data['nonce'] ) || ! isset( $post_data['booking_id'] ) ) {
				wp_die();
			}
			if ( ! wp_verify_nonce( $post_data['nonce'], 'el_ticket_list_nonce' ) ) {
				wp_die();
			}

			$current_user_id 		= get_current_user_id();
			$allow_transfer_ticket 	= EL()->options->ticket_transfer->get('allow_transfer_ticket','');
			$booking_id 			= isset( $post_data['booking_id'] ) ? sanitize_text_field( $post_data['booking_id'] ) : '';

			if ( $current_user_id != get_post_meta( $booking_id, OVA_METABOX_EVENT.'id_customer', true ) ) {
				wp_die();
			}

			$list_tickets = EL_Ticket::instance()->get_list_ticket_by_id_booking( $booking_id );

			ob_start();
			if ( $list_tickets ) :
				foreach ( $list_tickets as $ticket ) :
					$ticket_id 		= $ticket->ID;
					$ticket_transfer = get_post_meta( $ticket_id, OVA_METABOX_EVENT.'transfer_status', true );
					$ticket_status 	= get_post_meta( $ticket_id, OVA_METABOX_EVENT.'ticket_status', true );
					$customer 		= get_post_meta( $ticket_id, OVA_METABOX_EVENT.'name_customer', true );

					$arr_venue 	= get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'venue', true );
					$address 	= get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'address', true );

					$venue = is_array( $arr_venue ) ? implode(", ", $arr_venue) : $arr_venue;
					$venue_address = '';
					if( !empty( $venue ) ){
						$venue_address .= sprintf( esc_html__( 'Venue: %s', 'eventlist' ), $venue );
					}
					if( $address ){
						if ( $venue_address ) {
							$venue_address .= ';';
						}
						$venue_address .= sprintf( esc_html__( 'Address: %s', 'eventlist' ), $address );
					}

					$ticket_seat 	= get_post_meta( $ticket_id, OVA_METABOX_EVENT.'seat', true );
					$ticket_qr 		= get_post_meta( $ticket_id, OVA_METABOX_EVENT.'qr_code', true );
					$start_date 	= get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_start', true );
					$end_date 		= get_post_meta( $ticket_id, OVA_METABOX_EVENT . 'date_end', true );
					$date_format 	= get_option('date_format');
					$time_format 	= get_option('time_format');

					$start_date_time = date_i18n($date_format, $start_date) . ' - ' . date_i18n($time_format, $start_date);
					$end_date_time = date_i18n($date_format, $end_date) . ' - ' . date_i18n($time_format, $end_date);

					$person_type = get_post_meta( $ticket_id, OVA_METABOX_EVENT.'person_type', true );
					if ( $person_type ) {
						$ticket_seat .= ' - '.$person_type;
					}

					?>
					<tr>
						<?php if ( $allow_transfer_ticket ): ?>
							<th scope="row">
								<?php if ( $ticket_status !== 'checked' && $ticket_transfer !== 'yes' ): ?>
									<div class="form-check">
										<input class="form-check-input position-static ticket_check" type="checkbox" value="<?php echo esc_attr( $ticket_id ); ?>" id=ticket_check_id<?php echo esc_attr( $ticket_id ); ?> aria-label="<?php esc_attr_e( 'Check ticket', 'eventlist' ); ?>">
									</div>
								<?php endif; ?>
							</th>
						<?php endif; ?>
						<td><?php echo esc_html( $ticket_id ); ?></td>
						<td><?php echo esc_html( get_the_title( $ticket_id ) ); ?></td>
						<td><?php echo esc_html( $customer ); ?></td>
						<td><?php echo esc_html( $ticket_status ); ?></td>
						<td><?php echo esc_html( $ticket_seat ); ?></td>
						<td><?php echo esc_html( $venue_address ); ?></td>
						<td>
							<div class="ticket_qr_wrap">
								<button class="ticket_qr_toggle"><i class="fas fa-eye"></i></button>
								<span class="ticket_qr"><?php echo esc_html( $ticket_qr ); ?></span>
							</div>
						</td>
						<td><?php echo esc_html( $start_date_time ); ?></td>
						<td><?php echo esc_html( $end_date_time ); ?></td>
						<?php
						$site_url = get_bloginfo( 'url' );
						$url = add_query_arg( 'post_type', 'event', $site_url );
						$url = add_query_arg( 'id_ticket', $ticket_id, $url );
						$url = add_query_arg( 'qr_code', $ticket_qr, $url );
						$url = add_query_arg( 'customer_check_qrcode', 'true', $url );
						$url = add_query_arg( '_nonce', wp_create_nonce( 'el_check_qrcode' ), $url );
						?>
						<td><a href="<?php echo esc_url( $url ); ?>" target="_blank" class="btn btn-link"><?php esc_html_e( 'Check', 'eventlist' ); ?></a></td>
					</tr>
					<?php
				endforeach;
			else :
				$colspan = $allow_transfer_ticket ? '11' : '10';
				?>
				<tr>
					<td colspan="<?php echo esc_attr( $colspan ); ?>"><?php esc_html_e( 'Ticket not found', 'eventlist' ); ?></td>
				</tr>
				<?php
			endif;
			wp_reset_postdata();
			echo ob_get_clean();
			wp_die();
		}

		public function el_ticket_transfer(){
			$current_user_id = get_current_user_id();

			$post_data = $_POST;
			$response = [];
			$response['status'] = 'error';
			$response['class'] = 'danger';
			$response['mail'] = 'false';
			$response['mail_mess'] = '';

			if ( ! $post_data['nonce'] || ! wp_verify_nonce( $post_data['nonce'], 'el_ticket_transfer_nonce' ) ) {
				$response['mess'] = esc_html__( 'Invalid nonce, please refresh your screen and try again.', 'eventlist' );
				$response = json_encode( $response );
				echo $response;
				wp_die();
			}
			
			$ticket_ids = $post_data['ticket_ids'] ? json_decode( stripslashes( $post_data['ticket_ids'] ) ) : '';
			$email 		= $post_data['email'] ? sanitize_text_field( $post_data['email'] ) : '';
			$name 		= $post_data['name'] ? sanitize_text_field( $post_data['name'] ) : '';
			$phone 		= $post_data['phone'] ? sanitize_text_field( $post_data['phone'] ) : '';
			$booking_id = $post_data['booking_id'] ? sanitize_text_field( $post_data['booking_id'] ) : '';

			if ( $current_user_id != get_post_meta( $booking_id, OVA_METABOX_EVENT.'id_customer', true ) ) {
				$response['mess'] = esc_html__( 'You do not have permission to use this function.', 'eventlist' );
				$response = json_encode( $response );
				echo $response;
				wp_die();
			}

			if ( $ticket_ids && $email && $name && $phone ) {
				$current_user 		= wp_get_current_user();
				$ticket_recipient 	= get_user_by( 'email', $email );
				$user_name 			= get_user_by( 'login', $email );

				$allow_create_user 		= EL_Setting::instance()->ticket_transfer->get('ticket_transfer_create_user', '');
				$change_customer_name 	= EL_Setting::instance()->ticket_transfer->get('ticket_transfer_change_customer_name', '');
				$add_transfer_text 		= EL_Setting::instance()->ticket_transfer->get('ticket_transfer_add_transfer', '');
				$transfer_text 			= esc_html__( '(transfer)', 'eventlist' );

				if ( $current_user->user_email === $email ) {
					$response['mess'] = esc_html__( 'Email address is not valid.', 'eventlist' );
					$response = json_encode( $response );
					echo $response;
					wp_die();
				}

				// handle create new user
				if ( $allow_create_user ) {

					if ( ! $user_name && ! $ticket_recipient ) {

						$random_password = wp_generate_password();
						$user_id = wp_create_user( $email, $random_password, $email );
						update_user_meta( $user_id, 'first_name', $name );
						update_user_meta( $user_id, 'user_phone', $phone );
						
						$send_mail = el_mail_reset_password( $user_id );

						if ( ! $send_mail ) {
							$response['mess'] = esc_html__( 'An error occurred while sending email.', 'eventlist' );
							$response = json_encode( $response );
							echo $response;
							wp_die();
						}

						$response['mail'] = 'true';
						$response['mail_mess'] = sprintf( esc_html__( 'The password reset link has been sent to email %s', 'eventlist' ), $ticket_recipient->user_email );

					}

				} else {

					if ( ! $user_name && ! $ticket_recipient ) {
						$response['mess'] = esc_html__( 'Email is not exist. Please check surely you created an account with this email address.', 'eventlist' );
						$response = json_encode( $response );
						echo $response;
						wp_die();
					}
				}

				// update transfer status
				foreach ( $ticket_ids as $ticket_id ) {
					$customer_name = get_post_meta( $ticket_id, OVA_METABOX_EVENT.'name_customer', true );
					$new_name = $customer_name;

					update_post_meta( $ticket_id, OVA_METABOX_EVENT.'transfer_status', 'yes' );
					update_post_meta( $ticket_id, OVA_METABOX_EVENT.'transfer_email', $email );

					if ( $change_customer_name ) {
						$new_name = $name;
					}
					if ( $add_transfer_text ) {
						$new_name = $new_name .' '.$transfer_text;
					}
					if ( $new_name != $customer_name ) {
						update_post_meta( $ticket_id, OVA_METABOX_EVENT.'name_customer', $new_name );
					}
				}

				$response['status'] = 'success';
				$response['mess'] = esc_html__( 'Ticket transfer successful.', 'eventlist' );
				$response['class'] = 'success';
				$response = json_encode( $response );
				echo $response;
				wp_die();

			} else {

				$response['mess'] = esc_html__( 'Please complete all information.', 'eventlist' );
				$response = json_encode( $response );
				echo $response;
				wp_die();

			}
			wp_die();
		}

		public function el_payment_countdown(){
			$response = '';
			$checkout_holding_ticket = EL()->options->checkout->get('checkout_holding_ticket', 'no');

			if ( $checkout_holding_ticket === 'yes' ) {

				$time_countdown_checkout = intval( EL()->options->checkout->get('max_time_complete_checkout', 600) );
				$booking_id = $event_id = $id_cal = '';
				$redirect = home_url();

				$booking_id = isset( $_POST['booking_id'] ) ? sanitize_text_field( $_POST['booking_id'] ) : '';
				$event_id = isset( $_POST['ide'] ) ? sanitize_text_field( $_POST['ide'] ) : '';
				$id_cal = isset( $_POST['idcal'] ) ? sanitize_text_field( $_POST['idcal'] ) : '';


				if ( $booking_id ) {
					$event_id = get_post_meta( $booking_id, 'ova_mb_event_id_event', true );

					if ( $event_id ) {
						$redirect = get_permalink( $event_id );
					}
				}

				if ( $time_countdown_checkout && $booking_id ) {
					$time_sumbit_checkout = get_post_meta( $booking_id, OVA_METABOX_EVENT.'time_countdown_checkout', true );
					$current_time = current_time( 'timestamp' );
					$past_time = absint( $current_time ) - absint( $time_sumbit_checkout );
					$time_countdown_checkout -= $past_time;

					if ( $time_countdown_checkout < 0 ) {
						$time_countdown_checkout = 0;
					}

					if ( $time_countdown_checkout == 0 ) {
						wp_redirect( $redirect );
						exit;
					}

					$minutes = absint( $time_countdown_checkout / 60 );
					$seconds = absint( $time_countdown_checkout % 60 );
					if ( $minutes < 10 ) {
						$minutes = '0'.$minutes;
					}
					if ( $seconds < 10 ) {
						$seconds = '0'.$seconds;
					}
					ob_start();
					?>
					<div 
					class="countdown-checkout" 
					data-time-countdown-checkout="<?php esc_attr_e( $time_countdown_checkout ); ?>" 
					data-redirect="<?php echo esc_url( $redirect ); ?>" 
					data-booking-id="<?php esc_attr_e( $booking_id ); ?>" 
					data-event-id="<?php esc_attr_e( $event_id ); ?>" 
					data-id-cal="<?php esc_attr_e( $id_cal ); ?>" 
					data-countdown-checkout-nonce="<?php echo wp_create_nonce( 'el_countdown_checkout_nonce' ); ?>">
					<div class="countdown-time">
						<span class="text"><?php echo esc_html__( 'Your remaining time is ', 'eventlist' ); ?></span>
						<span class="time"><?php echo esc_html( $minutes.':'.$seconds ); ?></span>
						<span class="unit"><?php echo esc_html__( ' minutes to complete your payment', 'eventlist' ) ?></span>
					</div>
				</div>
				<?php
				$response = ob_get_clean();
				echo $response;
				wp_die();
			}
		}
		echo $response;
		wp_die();
	}
}

	new El_Ajax();
}

?>