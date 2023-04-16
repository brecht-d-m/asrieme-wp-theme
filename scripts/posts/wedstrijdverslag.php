<?php

require_once 'class-wedstrijdverslag.php';
use Wedstrijdverslag\Wedstrijdverslag;

function _get_wedstrijdverslag_auteur( bool $minimaal ) : string {
    $auteur = get_field( 'wedstrijdverslag_auteur' );
    if( empty( $auteur ) ) {
        $auteur = $minimaal ? get_the_author_meta( 'first_name' ) : get_the_author();
    }
    return $auteur == NULL ? '' : $auteur;
}

function _get_wedstrijdverslag_datum() : string {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );
    $wedstrijd_datum = get_field( 'activiteit_datum', $wedstrijd_id );
    $wedstrijd_datum = $wedstrijd_datum == NULL ? '' : get_the_date( 'Ymd' );
    return $wedstrijd_datum == NULL ? '' : $wedstrijd_datum;
}

function _create_wedstrijdverslag_suffix_infobar() : string {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );

    $meta_knoppen = '';
    $meta_knoppen .= _create_resultaat_card( $wedstrijd_id );
    $meta_knoppen .= _create_album_card( $wedstrijd_id );

    return 
        "<div class='d-flex flex-column flex-md-row mt-4 overflow-scroll'>
            $meta_knoppen
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

function _create_minimal_related_wedstrijdverslag_card( Wedstrijdverslag $post ) : string {
    $uitgelichte_afbeelding = '';

    if ( $post->uitgelichte_afbeelding_id != NULL) {
        $foto_wrapper = wp_get_attachment_image( $post->uitgelichte_afbeelding_id, 'large', false, array( 'class' => 'object-fit-cover rounded-top' ) );
    } else {
        $foto_wrapper = 
            "<div class='w-100 text-center no-image-wrapper'>
                <img src='https://asrieme.be/wp-content/uploads/2020/09/asrieme-logo-full-color-rgb.png' class='no-image p-3 mx-auto d-block'>
            </div>";
    }

    $uitgelichte_afbeelding =
        "<div class='ratio ratio-21x9 rounded'>
            $foto_wrapper
        </div>";

    $titel = $post->titel;
    $meta_data = $post->get_meta_data_info();
    return
        "<div class='related-post wedstrijdverslag my-2 d-flex flex-column'>
            <a href='$post->link' title='$titel' class='text-decoration-none rounded'>
                <div class='foto-wrapper d-flex align-items-center justify-content-center rounded-top'>
                    $uitgelichte_afbeelding
                    <div class='meer-lezen rounded px-1 ms-2 d-flex'>
                        <div>Meer lezen</div>
                        <div class='icon mx-2'><i class='fas fa-chevron-right'></i></div>
                    </div>
                </div>
                <div class='post-info-card pt-2 px-2 d-flex flex-column'>
                    <div class='text-muted'>$meta_data</div>
                    <h3><strong>$titel</strong></h3>
                </div>
            </a>
        </div>";
}

?>