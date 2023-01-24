<?php

require_once 'class-wedstrijdverslag.php';

use Wedstrijdverslag\Wedstrijdverslag;

function wedstrijdverslag_subtitel_func() {
    return _post_subtitel( 'wedstrijdoverzicht/verslagenarchief/', 'Verslagen' );
}
add_shortcode( 'wedstrijdverslag_subtitel', 'wedstrijdverslag_subtitel_func' );

function westrijdverslagen_zoek_container_func() : string {
    return _posts_zoek_container( 'wedstrijdoverzicht/verslagenarchief/' );
}
add_shortcode( 'westrijdverslagen_zoek_container', 'westrijdverslagen_zoek_container_func' );

function westrijdverslagen_archief_container_func() : string {
    $verslagen_query = new WP_Query( _get_posts_args( 'wedstrijdverslag' ) );
    $verslagen = array();
    if( $verslagen_query->have_posts() ) {
        while( $verslagen_query->have_posts() ) {
            $verslagen_query->the_post();
            array_push( $verslagen, _create_wedstrijdverslag() );
        }
    }
    wp_reset_postdata();

    if( empty( $verslagen ) ) {
        return 
            "<div class='posts-container d-flex justify-content-center my-4'>
                Geen wedstrijdverslagen teruggevonden...
            </div>";
    }

    $verslagen_container = '';
    foreach( $verslagen as $v => $verslag ) {
        $verslagen_container .= $verslag->create_wedstrijdverslag_card();
    }    

    return 
        "<div class='posts-container row gy-4'>
            $verslagen_container
        </div>";
}
add_shortcode( 'westrijdverslagen_archief', 'westrijdverslagen_archief_container_func' );

function _create_wedstrijdverslag() : Wedstrijdverslag {
    $wedstrijdverslag = new Wedstrijdverslag();
    $wedstrijdverslag->set_titel( get_the_title() );
    // eerst activiteit datum, dan get_the_date
    $wedstrijdverslag->set_datum( get_the_date( 'j F' ) );
    $wedstrijdverslag->set_link( get_the_permalink() );
    $wedstrijdverslag->set_gerelateerde_wedstrijd_id( get_field( 'wedstrijdverslag_wedstrijd' ) );
    $wedstrijdverslag->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    return $wedstrijdverslag;
}

function infobar_container_func() {

}
add_shortcode( 'infobar_container', 'infobar_container_func' );

?>