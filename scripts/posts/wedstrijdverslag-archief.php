<?php

require_once 'class-wedstrijdverslag.php';
use Wedstrijdverslag\Wedstrijdverslag;

function _create_wedstrijdverslagen_zoek_container() : string {
    return _posts_zoek_container( 'wedstrijdoverzicht/verslagenarchief/' );
}

function _get_wedstrijdverslag_type() : string {
    return 'wedstrijdverslag';
}

function _create_wedstrijdverslag() : Wedstrijdverslag {
    $wedstrijdverslag = new Wedstrijdverslag();
    $wedstrijdverslag->set_titel( get_the_title() );
    $wedstrijdverslag->set_datum( get_the_date( 'j F' ) );
    $wedstrijdverslag->set_link( get_the_permalink() );
    $wedstrijdverslag->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    $wedstrijdverslag->set_gerelateerde_wedstrijd_id( get_field( 'wedstrijdverslag_wedstrijd' ) );
    return $wedstrijdverslag;
}

function _get_no_wedstrijdverslag_label() : string {
    return 'Geen wedstrijdverslagen teruggevonden...';
}

?>