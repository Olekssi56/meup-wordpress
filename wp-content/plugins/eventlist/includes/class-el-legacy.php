<?php

if ( ! defined('ABSPATH') ) {
	exit();
}

if ( ! class_exists('EL_Legacy') ) {
	
	class EL_Legacy {

		public function __construct(){

			if ( apply_filters( 'el_update_event_min_max_price', false ) === true ) {
				add_action( 'admin_init', array( $this, 'el_update_event_min_max_price' ) );
			}

		}

		public function el_update_event_min_max_price(){
			$args = array(
				'post_type' => 'event',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'fields' => 'ids',
			);
			$events = get_posts( $args );

			if ( count( $events ) > 0 ) {

				foreach ( $events as $event_id ) {
					$ticket_prices = array();
					$ticket_link 	= get_post_meta( $event_id, OVA_METABOX_EVENT.'ticket_link', true );
					$ticket_external_link_price = get_post_meta( $event_id, OVA_METABOX_EVENT.'ticket_external_link_price', true );
					$seat_option 	= get_post_meta( $event_id, OVA_METABOX_EVENT.'seat_option', true );
					$ticket 		= get_post_meta( $event_id, OVA_METABOX_EVENT.'ticket', true );
					$ticket_map 	= get_post_meta( $event_id, OVA_METABOX_EVENT.'ticket_map', true );

					if ( $ticket_link !== 'ticket_internal_link' ) {
						if ( $ticket_external_link_price  ) {
							$ticket_prices['ticket_external_link'][] = preg_replace('/[^0-9]/', '', $ticket_external_link_price);

						}
					}

					if ( ! empty( $ticket ) ) {
						foreach ($ticket as $key => $value) {
							if ( isset( $value['price_ticket'] ) ) {
								$ticket_prices['none'][] = (float) $value['price_ticket'];
								$ticket_prices['simple'][] = (float) $value['price_ticket'];
							}
						}
					}

					if ( ! empty( $ticket_map ) ) {
						if ( isset ( $ticket_map['seat'] ) && ! empty( $ticket_map['seat'] ) ) {
							foreach ( $ticket_map['seat'] as $key => $value ) {
								if ( $value['price'] ) {
									$ticket_prices['map'][] = (float) $value['price'];
								}
							}
							foreach ($ticket_map['area'] as $key => $value) {
								if ( $value['price'] ) {
									$ticket_prices['map'][] = (float) $value['price'];
								} elseif ( $value['person_price'] ) {
									$person_type = json_decode( $value['person_price'] );
									foreach ( $person_type as $price ) {
										$ticket_prices['map'][] = (float) $price;
									}
								}
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

					$min_price = get_post_meta( $event_id, OVA_METABOX_EVENT.'min_price', true );
					$max_price = get_post_meta( $event_id, OVA_METABOX_EVENT.'max_price', true );

					if ( $min_max_price != '' ) {
						$min_max_price = explode("-", $min_max_price);
						$min_max_price = array_map('floatval', $min_max_price);
						$min_price = min($min_max_price);
						$max_price = max($min_max_price);
					}

					update_post_meta( $event_id, OVA_METABOX_EVENT.'min_price', $min_price);
					update_post_meta( $event_id, OVA_METABOX_EVENT.'max_price', $max_price);
				}
			}
		}
	}

	new EL_Legacy();
}