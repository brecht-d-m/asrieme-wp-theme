<?php

require_once 'class-wedstrijdverslag.php';
use Wedstrijdverslag\Wedstrijdverslag;

function _get_wedstrijdverslagenarchief_link() : string {
    return 'wedstrijdoverzicht/verslagenarchief/';
}

function _get_wedstrijdverslag_type() : string {
    return 'wedstrijdverslag';
}

function _create_wedstrijdverslag() : Wedstrijdverslag {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );
    $wedstrijd_datum = get_field( 'activiteit_datum', $wedstrijd_id );
    if( !empty( $wedstrijd_datum ) ) {
        $datum = DateTime::createFromFormat( 'Ymd', $wedstrijd_datum )->getTimestamp();
        $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, 'd MMM' );
        $wedstrijd_datum = $format->format( $datum );
    } else {
        $wedstrijd_datum = '';
    }

    $wedstrijdverslag = new Wedstrijdverslag();
    $wedstrijdverslag->set_titel( get_the_title() );
    $wedstrijdverslag->set_datum( $wedstrijd_datum );
    $wedstrijdverslag->set_link( get_the_permalink() );
    $wedstrijdverslag->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    $wedstrijdverslag->set_gerelateerde_wedstrijd_id( $wedstrijd_id );
    return $wedstrijdverslag;
}

function _get_no_wedstrijdverslag_label() : string {
    return 'Geen wedstrijdverslagen teruggevonden...';
}

function _get_wedstrijdverslagenarchief_link_label() : string {
    return 'Lees nog meer wedstrijdverslagen in ons archief!';
}

?>