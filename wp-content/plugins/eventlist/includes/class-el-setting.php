<?php

/**
 * EventList Setup
 * @package EventList
 * @since 1.0
 */
defined( 'ABSPATH' ) || exit;

/**
 * Settings Class
 */
class EL_Setting{

	/**
	 * instance
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * $_options
	 * @var null
	 */
	public $_options = null;

	public $_id = null;

	/**
	 * prefix option name
	 * @var string
	 */
	public $_prefix = 'ova_eventlist';

	public function __construct( $prefix = null, $id = null ) {

		if ( $prefix ) {
			$this->_prefix = $prefix;
		}

		$this->_id = $id;

		// load options
		$this->options();

		// save, update setting
		add_filter( 'el_admin_menus', array( $this, 'setting_page' ), 10, 1 );
		// add_filter( 'el_admin_menus', array( $this, 'display_profit' ), 12, 1 );

		add_action( 'admin_init', array( $this, 'register_setting' ) );
	}

	public function __get( $id = null ) {
		$settings = apply_filters( 'el_settings_field', array() );
		if ( array_key_exists( $id, $settings ) ) {
			return $settings[ $id ];
		}

		return null;
	}
	
	

	public function register_setting() {
		register_setting( $this->_prefix, $this->_prefix );
	}


	public function setting_page( $menus ){

		$manage_profit = EL()->options->tax_fee->get('manage_profit');
		

		$menus[] = array(
			'edit.php?post_type=event',
			__( 'Report Sales', 'eventlist' ),
			__( 'Report Sales', 'eventlist' ),
			'manage_options',
			'ova_el_display_report_sales',
			array( $this, 'register_report_sales_page' )
		);


		$menus[] = array(
			'edit.php?post_type=event',
			__( 'Report Users', 'eventlist' ),
			__( 'Report Users', 'eventlist' ),
			'manage_options',
			'ova_el_display_report_user',
			array( $this, 'register_report_user_page' )
		);


		if ($manage_profit == 'profit_1'){

			$menus[] = array(
				'edit.php?post_type=event',
				__( 'Manage Payouts', 'eventlist' ),
				__( 'Manage Payouts', 'eventlist' ),
				'manage_options',
				'ova_el_display_profit_event',
				array( $this, 'register_display_profit_event' )
			);
		}

		$menus[] = array(
			'edit.php?post_type=event',
			__( 'Custom Checkout Field', 'eventlist' ),
			__( 'Custom Checkout Field', 'eventlist' ),
			'manage_options',
			'ova_el_custom_checkout_field',
			array( $this, 'el_register_custom_checkout_field' )
		);

		$menus[] = array(
			'edit.php?post_type=event',
			__( 'Event List Settings', 'eventlist' ),
			__( 'Settings', 'eventlist' ),
			'manage_options',
			'ova_el_setting',
			array( $this, 'register_options_page' )
		);

		$menus[] = array(
			'edit.php?post_type=el_tickets',
			__( 'Replace Ticket Date', 'eventlist' ),
			__( 'Replace Date', 'eventlist' ),
			'manage_options',
			'el_replace_ticket_date',
			array( $this, 'el_replace_ticket_date' )
		);

		return $menus;
	}

	public function el_register_custom_checkout_field() {
		EL()->_include( EL_PLUGIN_INC . 'admin/views/settings/el_custom_checkout_field.php' );
	}

	public function register_display_profit_event() {
		EL()->_include( EL_PLUGIN_INC . 'admin/views/settings/display_profit.php' );
	}

	public function el_replace_ticket_date() {
		EL()->_include( EL_PLUGIN_INC . 'admin/views/settings/el_replace_ticket_date.php' );
	}

	/**
	 * register option page
	 * @return
	 */
	public function register_options_page() {
		EL()->_include( EL_PLUGIN_INC . 'admin/views/settings/settings.php' );
	}


	/**
	 * register report page
	 * @return
	 */
	public function register_report_sales_page() {
		EL()->_include( EL_PLUGIN_INC . 'admin/views/settings/report_sales.php' );
	}


	/**
	 * register report page
	 * @return
	 */
	public function register_report_user_page() {
		EL()->_include( EL_PLUGIN_INC . 'admin/views/settings/report_user.php' );
	}

	/**
	 * options load options
	 * @return array || null
	 */
	protected function options() {
		if ( $this->_options ) {
			return $this->_options;
		}

		return $this->_options = get_option( $this->_prefix, null );
	}

	/**
	 * get option value
	 *
	 * @param  $name
	 *
	 * @return option value. array, string, boolean
	 */
	public function get( $name = null, $default = null ) {
		if ( ! $this->_options ) {
			$this->_options = $this->options();
		}

		if ( $name && isset( $this->_options[ $name ] ) ) {
			return $this->_options[ $name ];
		}

		return $default;
	}
	
	static function instance( $prefix = null, $id = null ) {

		if ( ! empty( self::$_instance[ $prefix ] ) ) {
			return self::$_instance[ $prefix ];
		}

		return self::$_instance[ $prefix ] = new self( $prefix, $id );
	}
	
}