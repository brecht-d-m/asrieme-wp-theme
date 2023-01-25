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

?>