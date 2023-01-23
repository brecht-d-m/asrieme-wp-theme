<?php
function my_theme_enqueue_styles() {

    $parent_style = 'divi-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get( 'Version' )
    );
    
    // Custom font package
    wp_register_style( 'fonts-social-style', get_stylesheet_directory_uri() . '/fonts/custom.css' );
    wp_enqueue_style( 'fonts-social-style' );
    // Custom menu theme
    wp_register_style( 'menu-style', get_stylesheet_directory_uri() . '/menu.css' );
    wp_enqueue_style( 'menu-style' );
    // Back button
    wp_register_style( 'back-button-style', get_stylesheet_directory_uri() . '/style/back-button.css' );
    wp_enqueue_style( 'back-button-style' );
    wp_register_style( 'info-container-style', get_stylesheet_directory_uri() . '/style/info-container.css' );
    wp_enqueue_style( 'info-container-style' );
    wp_register_style( 'rieme-socials-style', get_stylesheet_directory_uri() . '/style/socials.css' );
    wp_enqueue_style( 'rieme-socials-style' );
    wp_register_style( 'members-style', get_stylesheet_directory_uri() . '/style/members.css' );
    wp_enqueue_style( 'members-style' );
    wp_register_style( 'posts-style', get_stylesheet_directory_uri() . '/style/berichten.css' );
    wp_enqueue_style( 'posts-style' );

    wp_register_style( 'rieme-bestuur-style', get_stylesheet_directory_uri() . '/style/bestuur.css' );
    wp_enqueue_style( 'rieme-bestuur-style' );
    wp_register_style( 'rieme-wedstrijden-style', get_stylesheet_directory_uri() . '/style/wedstrijden.css' );
    wp_enqueue_style( 'rieme-wedstrijden-style' );
    wp_register_style( 'rieme-activiteiten-style', get_stylesheet_directory_uri() . '/style/activiteiten.css' );
    wp_enqueue_style( 'rieme-activiteiten-style' );
    wp_register_style( 'rieme-documenten-style', get_stylesheet_directory_uri() . '/style/documenten.css' );
    wp_enqueue_style( 'rieme-documenten-style' );
    wp_register_style( 'rieme-sponsors-style', get_stylesheet_directory_uri() . '/style/sponsors.css' );
    wp_enqueue_style( 'rieme-sponsors-style' );
    wp_register_style( 'rieme-klassementen-style', get_stylesheet_directory_uri() . '/style/klassementen.css' );
    wp_enqueue_style( 'rieme-klassementen-style' );
    wp_register_style( 'rieme-clubbladen-style', get_stylesheet_directory_uri() . '/style/clubbladen.css' );
    wp_enqueue_style( 'rieme-clubbladen-style' );
    wp_register_style( 'rieme-posts-style', get_stylesheet_directory_uri() . '/style/posts.css' );
    wp_enqueue_style( 'rieme-posts-style' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/* --------------------- */
/* General php-functions */
/* --------------------- */

include 'scripts/activiteiten/activiteiten.php';
include 'scripts/activiteiten/wedstrijden.php';
include 'scripts/activiteiten/evenementen.php';
include 'scripts/activiteiten/wedstrijdverslagen.php';
include 'scripts/clubwerking/bestuur.php';
include 'scripts/clubwerking/leden.php';
include 'scripts/clubwerking/sponsors.php';
include 'scripts/clubwerking/nieuwsberichten.php';
include 'scripts/lopen/trainers.php';
include 'scripts/lopen/klassementen.php';
include 'scripts/clubblad/clubblad.php';
include 'scripts/back-button.php';
include 'scripts/documenten.php';
include 'scripts/socials.php';
include 'scripts/posts.php';
include 'scripts/home.php';

function meta_value_func( $atts ) {
    $a = shortcode_atts( array( 'veld'  => '', 'pagina' => '' ), $atts );
    $veld = $a['veld'];
    if( empty( $veld ) ) {
        return '';
    }

    $meta_value = '';
    if( !empty( $a['pagina'] ) ) {
        $query = new WP_Query([
            'post_type' => 'page',
            'name' => $a['pagina']
        ]);
        if( $query->have_posts() ) {
            $query->the_post();
            $meta_value = get_field( $veld, get_the_ID() );
        }
        wp_reset_postdata();
    }

    return empty( $meta_value ) ? get_field( $veld ) : $meta_value;
}
add_shortcode( 'meta_value', 'meta_value_func' );

// Projecten niet weergeven in admin menu
add_filter( 'et_project_posttype_args', 'remove_project_admin_menu', 10, 1 );
function remove_project_admin_menu( $args ) {
	return array_merge( $args, array(
		'public'              => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => false
	) );
}
// Comments niet weergeven in admin menu & bar
add_action( 'admin_menu', 'remove_comments_admin_menu' );
function remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'init', 'remove_comment_support', 100);

function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
function remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'remove_comments_admin_bar' );

function remove_built_in_roles() {
    global $wp_roles;
    $roles_to_remove = array( 'subscriber', 'contributor', 'author', 'editor' );
    foreach( $roles_to_remove as $role ) {
        if( isset( $wp_roles->roles[$role] ) ) {
            $wp_roles->remove_role( $role );
        }
    }
}
add_action( 'admin_menu', 'remove_built_in_roles' );

// Filters voor verslagen en berichten artikelen
function add_blog_query_variables() { 
    global $wp; 
    $wp->add_query_var('j'); 
    $wp->add_query_var('z'); 
}
add_action('init','add_blog_query_variables');

?>