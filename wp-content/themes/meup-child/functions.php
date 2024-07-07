<?php
/**

 * Setup meup Child Theme's textdomain.

 *

 * Declare textdomain for this child theme.

 * Translations can be filed in the /languages/ directory.

 */

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