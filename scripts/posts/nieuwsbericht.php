<?php

require_once 'class-nieuwsbericht.php';
use Nieuwsbericht\Nieuwsbericht;

function _get_nieuwsbericht_auteur( bool $minimaal ) : string {
    $auteur = $minimaal ? get_the_author_meta( 'first_name' ) : get_the_author();
    return $auteur == NULL ? '' : $auteur;
}

function _get_nieuwsbericht_datum() : string {
    $nieuwsbericht_datum = get_the_date( 'Ymd' );
    return $nieuwsbericht_datum == NULL ? '' : $nieuwsbericht_datum;
}

function _create_nieuwsbericht_suffix_infobar() : string {
    return '';
}

function _get_gerelateerde_nieuwsberichten() : array {
    $nieuwsbericht_args = array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'orderby'        => 'publish_date', 
        'order'          => 'DESC'
    );
    $gerelateerde_nieuwsberichten = array();
    $nieuwsberichten_query = new WP_Query( $nieuwsbericht_args );
    if( $nieuwsberichten_query->have_posts() ) {
        while( $nieuwsberichten_query->have_posts() ) {
            $nieuwsberichten_query->the_post();
            $nieuwsbericht = _create_nieuwsbericht();
            array_push( $gerelateerde_nieuwsberichten, $nieuwsbericht );
        }
    }

    return $gerelateerde_nieuwsberichten;
}

function _get_gerelateerde_nieuwsberichten_titel() : string {
    return 'Laatste nieuws';
}

function _create_minimal_related_nieuwsbericht_card( Nieuwsbericht $post ) : string {
    $titel = $post->titel;
    $meta_data = $post->get_meta_data_info();
    return
        "<div class='related-post nieuwsbericht my-2 d-flex flex-column'>
            <a href='$post->link' title='$titel' class='text-decoration-none rounded'>
                <div class='post-info-card pt-2 px-2 d-flex flex-column'>
                    <h3 class='mb-1 pb-0'><strong>$titel</strong></h3>
                    <div class='text-muted'>$meta_data</div>
                </div>
            </a>
        </div>";
}

?>