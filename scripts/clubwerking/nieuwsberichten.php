<?php

require_once 'class-nieuwsbericht.php';

use Nieuwsbericht\Nieuwsbericht;

function _create_nieuwsbericht_subtitel() : string {
    return _post_subtitel( '', 'Homepagina' );
}

function _create_nieuwsberichten_zoek_container() : string {
    return _posts_zoek_container( 'berichtenarchief/' );
}

function _create_nieuwsberichten_archief_container() : string {
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
        $berichten_container .= $bericht->create_archief_card();
    }    

    return 
        "<div class='posts-container row gy-4'>
            $berichten_container
        </div>";
}

function _create_nieuwsbericht() : Nieuwsbericht {
    $nieuwsbericht = new Nieuwsbericht();
    $nieuwsbericht->set_titel( get_the_title() );
    $nieuwsbericht->set_datum( get_the_date( 'j F' ) );
    $nieuwsbericht->set_link( get_the_permalink() );
    $nieuwsbericht->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    return $nieuwsbericht;
}

function _create_nieuwsbericht_infobar_card( string $type_label ) : string {
    $titel = get_the_title();
    $samenvatting = get_the_excerpt();
    $auteur = get_the_author();
    $uitgelichte_afbeelding = _get_infobar_image();
    return 
        "<div class='d-flex flex-column'>
            $uitgelichte_afbeelding
            <div class='post-info-card pt-3 d-flex flex-column'>
                <div><h1><strong>$titel</strong></h1></div>
                <div class='lead'>$samenvatting</div>
                <div class='small mt-3 text-muted'>$type_label door $auteur</div>
                <hr>
            </div>
        </div>";
}

?>