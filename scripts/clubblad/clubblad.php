<?php

require_once 'class-clubblad.php';
require_once 'class-clubblad-card-properties.php';

use Clubblad\Clubblad;
use Clubblad\Clubblad_Card_Properties;

function laatste_clubblad_func() {
    $laatste_clubblad = NULL;
    $query = new WP_Query( _get_clubbladen_args( 1 ) );
    if( $query->have_posts() ) {
        $query->the_post();
        $laatste_clubblad = _create_clubblad();
    }
    wp_reset_postdata();

    if( empty( $laatste_clubblad ) ) {
        return '';
    }

    $card_properties = new Clubblad_Card_Properties();
    $card_properties->set_card_minimaal( false );
    $card_properties->set_card_relative_width( 'col-12' );
    return $clubblad->create_clubblad_card( $card_properties );
}
add_shortcode( 'laatste_clubblad', 'laatste_clubblad_func' );

function clubbladen_container_func() {
    $laatste_clubbladen = array();
    $query = new WP_Query( _get_clubbladen_args( 11 ) );
    if( $query->have_posts() ) {
        while( $query->have_posts() ) {
            $query->the_post();
            array_push( $laatste_clubbladen, _create_clubblad() );
        }
    }
    wp_reset_postdata();

    if( count( $laatste_clubbladen )  <= 1 ) {
        return '';
    }
    // Meest recente clubblad verwijderen van container
    array_shift( $laatste_clubbladen );

    $card_properties = new Clubblad_Card_Properties();
    $card_properties->set_card_minimaal( true );
    $card_properties->set_card_relative_width( 'col-4' );
    
    $clubblad_container = '';
    foreach( $clubblad as $c => $clubblad ) {
        $clubblad_container .= $clubblad->create_clubblad_card( $card_properties );
    }

    return 
        "<div class='clubbladen-container row'>
            $clubblad_container
        </div>";
}
add_shortcode( 'clubbladen_container', 'clubbladen_container_func' );

function _get_clubbladen_args( int $aantal ) : array {
    return array(
        'post_type'      => 'clubblad',
        'posts_per_page' => $aantal,
        'meta_key'       => 'clubblad_uitgave',
        'orderby'        => 'meta_key',
        'order'          => 'DESC'
    );
}

function _create_clubblad() : Clubblad {
    $clubblad = new Clubblad();
    $clubblad->set_titel( get_the_title() );
    $clubblad->set_uitgave( get_field( 'clubblad_uitgave' ) );
    $clubblad->set_uitgave_datum( get_field( 'clubblad_uitgave_datum' ) );
    $clubblad->set_inhoudstafel( get_field( 'clubblad_inhoudstafel' ) );
    if( has_post_thumbnail() ) {
        $clubblad->set_uitgelichte_afbeelding( get_the_post_thumbnail_id() );
    }
    return $clubblad;
}

?>