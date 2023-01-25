<?php

function _get_wedstrijdverslag_auteur() : string {
    $auteur = get_field( 'wedstrijdverslag_auteur' );
    if( empty( $auteur ) ) {
        $auteur = get_the_author();
    }
    return $auteur;
}

function _get_wedstrijdverslag_datum() : string {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );
    return get_field( 'activiteit_datum', $wedstrijd_id );
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

?>