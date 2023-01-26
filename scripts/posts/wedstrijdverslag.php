<?php

function _get_wedstrijdverslag_auteur() : string {
    $auteur = get_field( 'wedstrijdverslag_auteur' );
    if( empty( $auteur ) ) {
        $auteur = get_the_author();
    }
    return $auteur == NULL ? '' : $auteur;
}

function _get_wedstrijdverslag_datum() : string {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );
    $wedstrijd_datum = get_field( 'activiteit_datum', $wedstrijd_id );
    return $wedstrijd_datum == NULL ? '' : $wedstrijd_datum;
}

function _create_wedstrijdverslag_suffix_infobar() : string {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );

    $meta_knoppen = '';
    $meta_knoppen .= _create_resultaat_card( $wedstrijd_id );
    $meta_knoppen .= _create_album_card( $wedstrijd_id );

    return 
        "<div class='d-none d-sm-block'>
            <div class='d-flex mt-4 overflow-scroll'>
                $meta_knoppen
            </div>
        </div>";
}

function _get_gerelateerde_wedstrijdverslagen( int $wedstrijd_id ) : array {
    $huidig_wedstrijdverslag_id = -1;
    if( get_post_type() == 'wedstrijdverslag' ) {
        $huidig_wedstrijdverslag_id = get_the_ID();
    }

    $wedstrijdverslag_args = array(
        'post_type'      => 'wedstrijdverslag',
        'posts_per_page' => -1,
        'orderby'        => 'publish_date', 
        'order'          => 'DESC',
        'meta_query'     => array(
            array(
                'key'     => 'wedstrijdverslag_wedstrijd',
                'value'   => $wedstrijd_id
            )
        )
    );

    $gerelateerde_wedstrijdverslagen = array();
    $wedstrijdverslag_query = new WP_Query( $wedstrijdverslag_args );
    if( $wedstrijdverslag_query->have_posts() ) {
        while( $wedstrijdverslag_query->have_posts() ) {
            $wedstrijdverslag_query->the_post();
            $wedstrijdverslag_id = get_the_ID();
            if( $huidig_wedstrijdverslag_id != $wedstrijdverslag_id ) {
                $wedstrijdverslag = _create_wedstrijdverslag();
                array_push( $gerelateerde_wedstrijdverslagen, $wedstrijdverslag );
            }
        }
    }

    return $gerelateerde_wedstrijdverslagen;
}

function _get_gerelateerde_wedstrijdverslagen_titel() : string {
    return 'Gerelateerde verslagen';
}

?>