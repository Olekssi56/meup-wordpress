<?php
/**

 * Setup meup Child Theme's textdomain.

 *

 * Declare textdomain for this child theme.

 * Translations can be filed in the /languages/ directory.

 */

// Define the path to the mPDF autoload file
$mpdf_autoload_path = WP_CONTENT_DIR . '/plugins/eventlist/includes/ticket/mpdf/vendor/autoload.php';
$el_pdf_path = WP_CONTENT_DIR . '/plugins/eventlist/includes/ticket/class-el-pdf.php';

// Check if the file exists before including it
if (file_exists($mpdf_autoload_path)) {
	require_once $mpdf_autoload_path;
	require_once $el_pdf_path;
} else {
	// Handle the error appropriately
	error_log('mPDF autoload file not found: ' . $mpdf_autoload_path);
}


 function meup_child_theme_setup() {

	load_child_theme_textdomain( 'meup-child', get_stylesheet_directory() . '/languages' );

}

add_action( 'after_setup_theme', 'meup_child_theme_setup' );





// Add Code is here.



// Add Parent Style

add_action( 'wp_enqueue_scripts', 'meup_child_scripts', 100 );

function meup_child_scripts() {

    wp_enqueue_style( 'meup-parent-style', get_template_directory_uri(). '/style.css' );

}



add_filter( 'register_taxonomy_el_1', function ($params){ return array( 'slug' => 'eljob', 'name' => esc_html__( 'Job', 'meup-child' ) ); } );

add_filter( 'register_taxonomy_el_2', function ($params){ return array( 'slug' => 'eltime', 'name' => esc_html__( 'Time', 'meup-child' ) ); } );



function register_print_bulk_action($bulk_actions) {
    /**
     * Adds a custom bulk action to the 'el_tickets' post type edit screen.
     *
     * This function adds a new bulk action option to the dropdown menu on the 'el_tickets' post type edit screen.
     * The custom bulk action is labeled "Custom Bulk Action".
     *
     * @param array $bulk_actions The existing bulk actions.
     * @return array The updated bulk actions array with the new custom action added.
     */
    $bulk_actions['print'] = __('Print', 'textdomain');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-el_tickets', 'register_print_bulk_action');

// Handle the custom bulk action
function handle_print_bulk_action($redirect_to, $doaction, $post_ids) {
	if ($doaction !== 'print') {
		return $redirect_to;
	}

	// Generate the PDF
	$pdf_url = ( new EL_PDF )->create_pdf($post_ids);

	// Redirect to the PDF file
	$redirect_to = add_query_arg(array(
		'bulk_custom_action' => count($post_ids),
		'pdf_url' => urlencode($pdf_url)
	), $redirect_to);

	return $redirect_to;
}
add_filter('handle_bulk_actions-edit-el_tickets', 'handle_print_bulk_action');

// Provide feedback to the user
function print_bulk_action_admin_notice() {
	if (!empty($_REQUEST['bulk_custom_action']) && !empty($_REQUEST['pdf_url'])) {
		printf('<div id="message" class="updated fade"><p>' .
		       __('Bulk action completed. <a href="%s" target="_blank">Download PDF</a>', 'textdomain') . '</p></div>', esc_url($_REQUEST['pdf_url']));
	}
}
add_action('admin_notices', 'print_bulk_action_admin_notice');