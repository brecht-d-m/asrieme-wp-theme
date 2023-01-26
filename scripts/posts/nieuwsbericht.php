<?php

function _get_nieuwsbericht_auteur() : string {
    $auteur = get_the_author();
    return $auteur == NULL ? '' : $auteur;
}

function _get_nieuwsbericht_datum() : string {
    $nieuwsbericht_datum = get_the_date( 'Ymd' );
    return $nieuwsbericht_datum == NULL ? '' : $nieuwsbericht_datum;
}

function _create_nieuwsbericht_suffix_infobar() : string {
    return '';
}

function _get_gerelateerde_nieuwsberichten() : string {
    $nieuwsbericht_args = array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'post_status'    => 'publish',
        'orderby'        => 'publish_date', 
        'order'          => 'DESC'
    );
    $gerelateerde_nieuwsberichten = array();
    $nieuwsberichten_query = new WQ_Query( $nieuwsbericht_args );
    if( $nieuwsberichten_query->have_posts() ) {
        while( $nieuwsberichten_query->have_posts() ) {
            $nieuwsberichten_query->the_post();
            $nieuwsbericht = _create_nieuwsbericht();
            array_push( $gerelateerde_nieuwsberichten, $nieuwsbericht );
        }
    }

    return $gerelateerde_nieuwsberichten;
}

function _get_gerelateerde_nieuwsberichten_titel() : string {
    return 'Laatste nieuws';
}

?>