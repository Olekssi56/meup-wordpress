<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'El_Admin_Assets', false ) ) {
	return new El_Admin_Assets();
}

/**
 * Admin Assets classes
 */
class El_Admin_Assets{

	

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Constructor
	 */
	public function __construct(){

		add_action( 'admin_footer', array( $this, 'enqueue_scripts' ), 10, 2 );

	}

	/**
	 * Add menu items.
	 */
	public function enqueue_scripts() {

		if( EL()->options->general->get('event_google_key_map') ){
			wp_enqueue_script( 'google','//maps.googleapis.com/maps/api/js?key='.EL()->options->general->get('event_google_key_map').'&libraries=places&callback=Function.prototype', array('jquery'), false, true);
		}else{
			wp_enqueue_script( 'google','//maps.googleapis.com/maps/api/js?sensor=false&libraries=places&callback=Function.prototype', array('jquery'), false, true);
		}
		
		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		
		/* color picker */
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, true);

		$colorpicker = array(
			'clear' => __( 'Clear', 'eventlist' ),
			'defaultString' => __( 'Default', 'eventlist' ),
			'pick' => __( 'Select Color', 'eventlist' ),
			'current' => __( 'Current Color', 'eventlist' ),
		);
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker );



		/* Jquery UI */
		wp_enqueue_style( 'jquery-ui', EL_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.css' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );


		/* Datepicker */
		wp_enqueue_script( 'jquery-ui-datepicker' );
		if ( $cal_lang = el_calendar_language() ) {
			wp_enqueue_script('datepicker-lang', EL_PLUGIN_URI.'assets/libs/datepicker-lang/datepicker-'.$cal_lang.'.js', array('jquery'), false, true);
		}
		
		/* Select2 */
		wp_enqueue_script( 'select2', EL_PLUGIN_URI.'assets/libs/select2/select2.min.js' , array( 'jquery' ), null, true );
		wp_enqueue_style( 'select2', EL_PLUGIN_URI. 'assets/libs/select2/select2.min.css', array(), null );

		/* Jquery Timepicker */
		wp_enqueue_script('jquery-timepicker', EL_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.js', array('jquery'), false, true);
		wp_enqueue_style('jquery-timepicker', EL_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.css' );


		/* Elegant Font */
		wp_enqueue_style('elegant-font', EL_PLUGIN_URI.'assets/libs/elegant_font/ele_style.css', array(), null);

		wp_enqueue_style('v4-shims', EL_PLUGIN_URI.'/assets/libs/fontawesome/css/v4-shims.min.css', array(), null);
		wp_enqueue_style('fontawesome', EL_PLUGIN_URI.'assets/libs/fontawesome/css/all.min.css', array(), null);


		/* Validate */
		wp_enqueue_script('validate', EL_PLUGIN_URI.'assets/libs/jquery.validate.min.js', array('jquery'), false, true);
		
		
		/* Chart */
		wp_enqueue_script( 'el_flot', EL_PLUGIN_URI.'assets/libs/flot/jquery.flot.js', array('jquery'), null, true );
		wp_enqueue_script( 'el_flot_pie', EL_PLUGIN_URI.'assets/libs/flot/jquery.flot.pie.js', array('jquery'), null, true );
		wp_enqueue_script( 'el_flot_resize', EL_PLUGIN_URI.'assets/libs/flot/jquery.flot.resize.js', array('jquery'), null, true );
		wp_enqueue_script( 'el_flot_stack', EL_PLUGIN_URI.'assets/libs/flot/jquery.flot.stack.js', array('jquery'), null, true );
		wp_enqueue_script( 'el_flot_time', EL_PLUGIN_URI.'assets/libs/flot/jquery.flot.time.js', array('jquery'), null, true );

		wp_enqueue_script('el_admin', EL_PLUGIN_URI.'assets/js/admin/admin.min.js', array('jquery'), false, true);
		wp_localize_script( 'el_admin', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

		wp_enqueue_style('el_admin', EL_PLUGIN_URI.'assets/css/admin/admin.css' );
	}

	
}

El_Admin_Assets::instance();