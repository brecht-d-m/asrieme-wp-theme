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
    $nieuwsbericht = new Nieuwsbericht();
    $nieuwsbericht->set_titel( get_the_title() );
    $nieuwsbericht->set_datum( get_the_date( 'j F' ) );
    $nieuwsbericht->set_link( get_the_permalink() );
    $nieuwsbericht->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    return $nieuwsbericht;
}

function _get_no_nieuwsbericht_label() : string {
    return 'Geen nieuwsberichten teruggevonden...';
}

?>