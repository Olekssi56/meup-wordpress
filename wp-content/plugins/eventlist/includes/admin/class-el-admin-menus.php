<?php
/**
 * Setup menus in WP admin.
 *
 * @package EventList\Admin
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'El_Admin_Menus', false ) ) {
	return new El_Admin_Menus();
}

class El_Admin_Menus{

	/**
	 * instance
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * menu
	 * @var array
	 */
	public $_menus = array();



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

		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		

		
		/**
         * menus
         * @var
         */
		$menus = apply_filters( 'el_admin_menus', $this->_menus );
		foreach ( $menus as $menu ) {
			call_user_func_array( 'add_submenu_page', $menu );
		}


	}

	/**
     * add menu item
     * @param $params
     */
	public function add_menu( $params ) {
		$this->_menus[] = $params;
	}
	
}

El_Admin_Menus::instance();