<?php

require_once 'class-nieuwsbericht.php';
use Nieuwsbericht\Nieuwsbericht;

function _get_nieuwsberichtenarchief_link() : string {
    return 'berichtenarchief/';
}

function _get_nieuwsbericht_type() : string {
    return 'post';
}

function _create_nieuwsbericht() : Nieuwsbericht {
    $datum = DateTime::createFromFormat( 'Ymd', get_the_date( 'Ymd' ) )->getTimestamp();
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, 'd MMM' );
    $nieuwsbericht_datum = $format->format( $datum );

    $nieuwsbericht = new Nieuwsbericht();
    $nieuwsbericht->set_id( get_the_ID() );
    $nieuwsbericht->set_titel( get_the_title() );
    $nieuwsbericht->set_datum( $nieuwsbericht_datum );
    $nieuwsbericht->set_link( get_the_permalink() );
    $nieuwsbericht->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    return $nieuwsbericht;
}

function _get_no_nieuwsbericht_label() : string {
    return 'Geen nieuwsberichten teruggevonden...';
}

function _get_nieuwsberichtenarchief_link_label() : string {
    return 'Lees nog meer nieuwsberichten in ons archief!';
}

?>