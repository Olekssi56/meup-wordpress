<?php
/**
 * Enqueue parent and child theme styles
 */
function meup_child_enqueue_styles() {
    // Enqueue parent theme styles
    wp_enqueue_style('meup-parent-style', get_template_directory_uri() . '/style.css');
    
    // Enqueue child theme styles
    wp_enqueue_style('meup-child-style', get_stylesheet_directory_uri() . '/style.css', array('meup-parent-style'), wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'meup_child_enqueue_styles');

/**
 * Additional custom functions can be added below
 */

// Example custom function
function meup_child_custom_function() {
    // Your custom code here
}
// Uncomment the line below to enable the custom function
// add_action('init', 'meup_child_custom_function');
?>
