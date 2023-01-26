<?php

function post_infobar_card_func() {
    $post_type = get_post_type();

    $titel = get_the_title();
    $samenvatting = get_the_excerpt();
    
    $auteur = _get_infobar_auteur( $post_type );
    if( !empty( $auteur ) ) {
        $auteur_wrapper = 
            "<div class='d-flex small text-muted'>
                <span class='me-3'><i class='fas fa-pen'></i></span>
                <span>$auteur</span>
            </div>";
    } else {
        $auteur_wrapper = '';
    }
    
    $datum = _get_infobar_datum( $post_type );
    if( !empty( $datum ) ) {
        $datum_wrapper = 
            "<div class='d-flex small text-muted'>
                <span class='me-3'><i class='fas fa-calendar-alt'></i></span>
                <span>$datum</span>
            </div>";
    } else {
        $datum_wrapper = '';
    }

    $suffix_infobar_card = _get_suffix_infobar_card( $post_type );
    $uitgelichte_afbeelding = _get_infobar_image();

    return 
        "<div class='d-flex flex-column'>
            $uitgelichte_afbeelding
            <div class='post-info-card pt-3 d-flex flex-column'>
                <div><h1><strong>$titel</strong></h1></div>
                <div class='lead'>$samenvatting</div>
                <div class='mt-3 d-flex justify-content-between align-items-center'>
                    $auteur_wrapper
                    $datum_wrapper
                </div>
                $suffix_infobar_card
                <hr>
            </div>
        </div>";

    return $post_infobar_card;
}
add_shortcode( 'post_infobar_card', 'post_infobar_card_func' );

function _get_infobar_auteur( string $post_type ) : string {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $auteur = _get_wedstrijdverslag_auteur();
            break;
        case 'post':
            $auteur = _get_nieuwsbericht_auteur();
            break;
        default:
            $auteur = '';
            break;
    }
    return $auteur;
}

function _get_infobar_datum( string $post_type ) : string {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $post_datum = _get_wedstrijdverslag_datum();
            break;
        case 'post':
            $post_datum = _get_nieuwsbericht_datum();
            break;
        default:
            $post_datum = '';
            break;
    }

    if( empty( $post_datum ) ) {
        return '';
    }

    $datum = DateTime::createFromFormat( 'Ymd', $post_datum )->getTimestamp();
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, 'd MMMM yyyy' );
    return $format->format( $datum );
}

function _get_suffix_infobar_card( string $post_type ) : string {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $suffix_infobar_card = _create_wedstrijdverslag_suffix_infobar();
            break;
        case 'post':
            $suffix_infobar_card = _create_nieuwsbericht_suffix_infobar();
            break;
        default:
            $suffix_infobar_card = '';
            break;
    }
    return $suffix_infobar_card;
}

function _get_infobar_image() : string {
    $uitgelichte_afbeelding_id = get_post_thumbnail_id();
    $uitgelichte_afbeelding = '';
    if( !empty( $uitgelichte_afbeelding_id ) ) {
        $uitgelichte_afbeelding = wp_get_attachment_image( $uitgelichte_afbeelding_id, 'full', false, array( 'class' => 'object-fit-cover rounded' ) );
        $uitgelichte_afbeelding =
            "<div class='ratio ratio-21x9 rounded'>
                $uitgelichte_afbeelding
            </div>";
    }
    return $uitgelichte_afbeelding;
}

function gerelateerde_posts_func( $atts ) {
    $a = shortcode_atts( array( 'type' => '' ), $atts );
    switch( $a['type'] ) {
        case 'wedstrijdverslag':
            $gerelateerde_wedstrijd = get_field( 'wedstrijdverslag_wedstrijd' );
            $gerelateerde_posts = _get_gerelateerde_wedstrijdverslagen( $gerelateerde_wedstrijd );
            $gerelateerde_posts_titel = _get_gerelateerde_wedstrijdverslagen_titel();
            break;
        case 'wedstrijd':
            $gerelateerde_wedstrijd = get_the_ID();
            $gerelateerde_posts = _get_gerelateerde_wedstrijdverslagen( $gerelateerde_wedstrijd );
            $gerelateerde_posts_titel = _get_gerelateerde_wedstrijdverslagen_titel();
            break;
        case 'nieuwsbericht':
            $gerelateerde_posts = _get_gerelateerde_nieuwsberichten();
            $gerelateerde_posts_titel = _get_gerelateerde_nieuwsberichten_titel();
            break;
        default:
            $gerelateerde_posts = array();
            break;
    }

    if( empty( $gerelateerde_posts ) ) {
        return '';
    }

    $posts_container = '';
    foreach( $gerelateerde_posts as $p => $post ) {
        $uitgelichte_afbeelding = '';
        if( !empty( $post->uitgelichte_afbeelding_id ) ) {
            $uitgelichte_afbeelding = wp_get_attachment_image( $post->uitgelichte_afbeelding_id, 'large', false, array( 'class' => 'object-fit-cover rounded-top' ) );
            $uitgelichte_afbeelding =
                "<div class='ratio ratio-21x9 rounded'>
                    $uitgelichte_afbeelding
                </div>";
        }

        // TODO seperate per post_type? -> toestaan om meer lezen bij wedstrijdverslag te hebben
        $titel = $post->titel;
        $meta_data = $post->get_meta_data_info();
        $posts_container .= 
            "<div class='related-post d-flex flex-column rounded'>
                <a href='$post->link' class='text-decoration-none'>
                    $uitgelichte_afbeelding
                    <div class='post-info-card pt-2 px-2 d-flex flex-column'>
                        <div class='text-muted'>$meta_data</div>
                        <h3><strong>$titel</strong></h3>
                    </div>
                </a>
            </div>";
    }

    return 
        "<div class='d-flex small text-muted'>
            <h2><span class='me-3 strong'><i class='fas fa-file-alt'></i></span></h2>
            <h2><strong>$gerelateerde_posts_titel</strong></h2>
        </div>
        <div class='row'>
            <div class='col justify-content-center'>
                $posts_container
            </div>
        </div>";
}
add_shortcode( 'gerelateerde_posts', 'gerelateerde_posts_func' );

?>