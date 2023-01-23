<?php

require_once 'class-nieuwsbericht.php';

use Nieuwsbericht\Nieuwsbericht;

function nieuwsbericht_subtitel_func() {
    return _post_subtitel( '', 'Homepagina' );
}
add_shortcode( 'nieuwsbericht_subtitel', 'nieuwsbericht_subtitel_func' );

function nieuwsberichten_zoek_container_func() : string {
    return _posts_zoek_container( 'berichtenarchief/' );
}
add_shortcode( 'nieuwsberichten_zoek_container', 'nieuwsberichten_zoek_container_func' );

function nieuwsberichten_archief_container_func() : string {
    $berichten_query = new WP_Query( _get_posts_args( 'post' ) );
    $berichten = array();
    if( $berichten_query->have_posts() ) {
        while( $berichten_query->have_posts() ) {
            $berichten_query->the_post();
            array_push( $berichten, _create_nieuwsbericht() );
        }
    }
    wp_reset_postdata();

    if( empty( $berichten ) ) {
        return 
            "<div class='posts-container d-flex justify-content-center my-4'>
                Geen nieuwsberichten teruggevonden...
            </div>";
    }

    $berichten_container = '';
    foreach( $berichten as $b => $bericht ) {
        $berichten_container .= $bericht->create_nieuwsbericht_card();
    }    

    return 
        "<div class='posts-container row gy-4'>
            $berichten_container
        </div>";
}
add_shortcode( 'nieuwsberichten_archief', 'nieuwsberichten_archief_container_func' );

function _create_nieuwsbericht() : Nieuwsbericht {
    $nieuwsbericht = new Nieuwsbericht();
    $nieuwsbericht->set_titel( get_the_title() );
    $nieuwsbericht->set_datum( get_the_date( 'j F' ) );
    $nieuwsbericht->set_link( get_the_permalink() );
    $nieuwsbericht->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    return $nieuwsbericht;
}

?>