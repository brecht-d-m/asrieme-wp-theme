<?php

function post_infobar_card_func() {
    $post_type = get_post_type();

    $titel = get_the_title();
    $samenvatting = get_the_excerpt();
    $auteur = _get_infobar_auteur( $post_type );
    $datum = _get_infobar_datum( $post_type );
    $suffix_infobar_card = _get_suffix_infobar_card( $post_type );
    $uitgelichte_afbeelding = _get_infobar_image();

    return 
        "<div class='d-flex flex-column'>
            $uitgelichte_afbeelding
            <div class='post-info-card pt-3 d-flex flex-column'>
                <div><h1><strong>$titel</strong></h1></div>
                <div class='lead'>$samenvatting</div>
                <div class='mt-3 d-flex justify-content-between align-items-center'>
                    <div class='d-flex small text-muted'>
                        <span class='me-3'><i class='fas fa-pen'></i></span>
                        <span>$auteur</span>
                    </div>
                    <div class='d-flex small text-muted'>
                        <span class='me-3'><i class='fas fa-calendar-alt'></i></span>
                        <span>$datum</span>
                    </div>
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


?>