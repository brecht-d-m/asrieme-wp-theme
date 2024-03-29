<?php

function post_infobar_card_func() {
    $post_type = get_post_type();

    $titel = get_the_title();
    $samenvatting = get_the_excerpt();
    
    $auteur = _get_infobar_auteur( $post_type );
    $auteur_minimaal = _get_infobar_auteur( $post_type, true );
    if( !empty( $auteur ) ) {
        $auteur_wrapper = 
            "<div class='d-flex small text-muted'>
                <span class='me-3'><i class='fas fa-pen'></i></span>
                <div class='d-none d-sm-block'><span>$auteur</span></div>
                <div class='d-block d-sm-none'><span>$auteur_minimaal</span></div>
            </div>";
    } else {
        $auteur_wrapper = '';
    }
    
    $datum = _get_infobar_datum( $post_type );
    $datum_minimaal = _get_infobar_datum( $post_type, true );
    if( !empty( $datum ) ) {
        $datum_wrapper = 
            "<div class='d-flex small text-muted'>
                <span class='me-3'><i class='fas fa-calendar-alt'></i></span>
                <div class='d-none d-sm-block'><span>$datum</span></div>
                <div class='d-block d-sm-none'><span>$datum_minimaal</span></div>
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

function _get_infobar_auteur( string $post_type, bool $minimaal = false ) : string {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $auteur = _get_wedstrijdverslag_auteur( $minimaal );
            break;
        case 'post':
            $auteur = _get_nieuwsbericht_auteur( $minimaal );
            break;
        default:
            $auteur = '';
            break;
    }
    return $auteur;
}

function _get_infobar_datum( string $post_type, bool $minimaal = false ) : string {
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
    $datum_format = $minimaal ? 'd MMM' : 'd MMMM yyyy' ;
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, $datum_format );
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
    $type = $a['type'];
    switch( $type ) {
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
        switch( $a['type'] ) {
            case 'wedstrijdverslag':
                $posts_container .= _create_minimal_related_wedstrijdverslag_card( $post );
                break;
            case 'wedstrijd':
                $posts_container .= _create_minimal_related_wedstrijdverslag_card( $post );
                break;
            case 'nieuwsbericht':
                $posts_container .=  _create_minimal_related_nieuwsbericht_card( $post );
                break;
            default:
                break;
        }
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

function archief_link_container_func( $atts ) {
    $a = shortcode_atts( array( 'type'  => '' ), $atts );
    switch( $a['type'] ) {
        case 'wedstrijdverslag':
            $label = _get_wedstrijdverslagenarchief_link_label();
            $link = _get_wedstrijdverslagenarchief_link();
            break;
        case 'nieuwsbericht':
            $label = _get_nieuwsberichtenarchief_link_label();
            $link = _get_nieuwsberichtenarchief_link();
            break;
        default:
            $label = '';
            $link = '';
            break;
    }

    $link = get_home_url( NULL, $link );
    return 
        "<div class='archief-link-container p-2 ps-4'>
            <div class='row'>
                <div class='col-12 col-md-8 align-self-center'>
                    $label
                </div>
                <div class='col-12 col-md-4 align-self-center'>
                    <div class='actie-knop mt-3 mb-2 my-md-1'>
                        <a href='$link' target='_blank'>Archief</a>
                    </div>
                </div>
            </div>
        </div>";
} 
add_shortcode( 'archief_link_container', 'archief_link_container_func' );

?>