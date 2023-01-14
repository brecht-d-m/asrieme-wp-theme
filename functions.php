<?php
function my_theme_enqueue_styles() {

    $parent_style = 'divi-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri().'/style.css');
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
    
    // Custom font package
    wp_register_style('fonts-social-style', get_stylesheet_directory_uri().'/fonts/custom.css');
    wp_enqueue_style('fonts-social-style');
    // Custom menu theme
    wp_register_style('menu-style', get_stylesheet_directory_uri().'/menu.css');
    wp_enqueue_style('menu-style');
    // Back button
    wp_register_style('back-button-style', get_stylesheet_directory_uri().'/style/back-button.css');
    wp_enqueue_style('back-button-style');
    wp_register_style('info-container-style', get_stylesheet_directory_uri().'/style/info-container.css');
    wp_enqueue_style('info-container-style');
    
    wp_register_style('members-style', get_stylesheet_directory_uri().'/style/members.css');
    wp_enqueue_style('members-style');
    wp_register_style('posts-style', get_stylesheet_directory_uri().'/style/berichten.css');
    wp_enqueue_style('posts-style');

    wp_register_style('rieme-bestuur-style', get_stylesheet_directory_uri().'/style/bestuur.css');
    wp_enqueue_style('rieme-bestuur-style');
    wp_register_style('rieme-wedstrijden-style', get_stylesheet_directory_uri().'/style/wedstrijden.css');
    wp_enqueue_style('rieme-wedstrijden-style');
    wp_register_style('rieme-activiteiten-style', get_stylesheet_directory_uri().'/style/activiteiten.css');
    wp_enqueue_style('rieme-activiteiten-style');
    wp_register_style('rieme-documenten-style', get_stylesheet_directory_uri().'/style/documenten.css');
    wp_enqueue_style('rieme-documenten-style');
    wp_register_style('rieme-sponsors-style', get_stylesheet_directory_uri().'/style/sponsors.css');
    wp_enqueue_style('rieme-sponsors-style');
    wp_register_style('rieme-klassementen-style', get_stylesheet_directory_uri().'/style/klassementen.css');
    wp_enqueue_style('rieme-klassementen-style');
    wp_register_style('rieme-socials-style', get_stylesheet_directory_uri().'/style/socials.css');
    wp_enqueue_style('rieme-socials-style');
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/* --------------------- */
/* General php-functions */
/* --------------------- */

include 'scripts/activiteiten/activiteiten.php';
include 'scripts/activiteiten/wedstrijden.php';
include 'scripts/activiteiten/evenementen.php';
include 'scripts/clubwerking/bestuur.php';
include 'scripts/clubwerking/leden.php';
include 'scripts/clubwerking/sponsors.php';
include 'scripts/lopen/trainers.php';
include 'scripts/lopen/klassementen.php';
include 'scripts/back-button.php';
include 'scripts/documenten.php';
include 'scripts/socials.php';
include 'scripts/posts.php';


function copyright_footer_func() {
    $huidig_jaar = date('Y');
    return "<div class='copy-right'>&copy; $huidig_jaar AS Rieme VZW</div>";
}
add_shortcode( 'copyright_footer', 'copyright_footer_func' );

// Projecten niet weergeven in admin menu
add_filter( 'et_project_posttype_args', 'remove_project_admin_menu', 10, 1 );
function remove_project_admin_menu( $args ) {
	return array_merge( $args, array(
		'public'              => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => false
	));
}
// Comments niet weergeven in admin menu & bar
add_action( 'admin_menu', 'remove_comments_admin_menu' );
function remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action('init', 'remove_comment_support', 100);

function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
function remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'remove_comments_admin_bar' );

?>