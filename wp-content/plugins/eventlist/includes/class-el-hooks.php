<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EL_Hooks {

	public function __construct() {

		add_action( 'admin_init', array( $this, 'el_prevent_admin_access' ) );

		

		$check_event_status_first_time = EL()->options->general->get('event_status_first_time','');

		if ( $check_event_status_first_time ) {
			add_action( 'el_vendor_after_create_event', array( $this, 'el_update_event_status' ), 10, 1 );
			add_action( 'el_vendor_after_update_event', array( $this, 'el_update_event_status' ), 10, 1 );
			add_action( 'el_after_save_event_metabox', array( $this, 'el_update_event_status' ), 10, 1 );
		} else {
			add_action( 'el_after_update_event_status_manually', array( $this, 'el_update_event_status_first_time' ), 10, 0 );
			add_action( 'el_after_update_event_status_automatic', array( $this, 'el_update_event_status_first_time' ), 10, 0 );
		}

		
	}

	function el_prevent_admin_access(){

		add_filter( 'woocommerce_prevent_admin_access', array( $this, 'el_woocommerce_prevent_admin_access_customize' ), 10, 1 );
		
	}

	public function el_woocommerce_prevent_admin_access_customize( $prevent_access ){

		if( el_can_upload_files() ){
			return false; 
		}

		return $prevent_access;

	}
	

	

	public function el_update_event_status_first_time(){
		$ova_event_setting = get_option( 'ova_eventlist' ) ? get_option( 'ova_eventlist' ) : array();
		$ova_event_setting['general']['event_status_first_time'] = 'pass';
		update_option( 'ova_eventlist', $ova_event_setting );

	}

	public function el_update_event_status( $post_id ){
		$end_date_time 		= (int) get_post_meta( $post_id, OVA_METABOX_EVENT.'end_date_str', true );
		$start_date_time 	= (int) get_post_meta( $post_id, OVA_METABOX_EVENT.'start_date_str', true );
		$option_calendar 	= get_post_meta( $post_id, OVA_METABOX_EVENT.'option_calendar', true );
		$current_time 		= (int) current_time( 'timestamp' );
		$event_status 		= '';

		if ( $end_date_time < $current_time ) {
			$event_status = 'past';
		} elseif ( $end_date_time > $current_time && ( $start_date_time >  $current_time || $option_calendar == 'auto' ) ) {
			$event_status = 'upcoming';
		} elseif ( $start_date_time <= $current_time && $end_date_time >= $current_time ) {
			$event_status = 'opening';
		}

		update_post_meta( $post_id, OVA_METABOX_EVENT.'event_status', $event_status );
	}
}

new EL_Hooks();