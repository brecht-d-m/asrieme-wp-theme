<?php

function _get_nieuwsbericht_auteur() : string {
    return get_the_author();
}

function _get_nieuwsbericht_datum() : string {
    return get_the_date( 'Ymd' );
}

function _create_nieuwsbericht_suffix_infobar() : string {
    return '';
}

?>