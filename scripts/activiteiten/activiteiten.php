<?php 

function activiteit_info_container_func() : string {
    $uitgelichte_afbeelding = _get_activiteit_image();

    $titel = get_field( 'activiteit_titel' );
    $subtitel = get_field( 'activiteit_subtitel' );

    $datum = _get_activiteit_meta_value( 'datum' );
    $datum = _format_activiteit_datum( $datum );
    $tijd = _get_activiteit_meta_value( 'tijd' );

    $datum_wrapper = _get_activiteit_meta_value_wrapper( $datum, 'fa-calendar-alt' );
    $tijd_wrapper = _get_activiteit_meta_value_wrapper( $tijd , 'fa-clock' );

    switch( get_post_type() ) {
        case 'wedstrijd':
            $suffix_activiteit_card = _create_wedstrijd_suffix_infobar();
            break;
        case 'evenement':
            $suffix_activiteit_card = _create_evenement_suffix_infobar();
            break;
        default:
            $suffix_infobar_card = '';
            break;
    }

    $titel = "<h1 class='activiteit-titel'><strong>$titel</strong></h1>";
    $subtitel = empty( $subtitel ) ? '' : "<h3 class='activiteit-subtitel'>$subtitel</h3>";

    return
        "<div class='d-flex flex-column'>
            $uitgelichte_afbeelding
            <div class='activiteit-info-card pt-3 d-flex flex-column'>
                $titel
                $subtitel
                <div class='mt-3 d-flex justify-content-between align-items-center'>
                    $datum_wrapper
                    $tijd_wrapper
                </div>
                $suffix_activiteit_card
                <hr>
            </div>
        </div>";
}
add_shortcode( 'activiteit_info_container', 'activiteit_info_container_func' );

function _get_activiteit_meta_value( $key ) : string {
    $activiteit_meta_value = get_field( 'activiteit_' . $key );
    if( empty( $activiteit_meta_value ) ) {
        return '';
    }

    return $activiteit_meta_value;    
}

function _get_activiteit_image() : string {
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

function _get_activiteit_meta_value_wrapper( $value, $fa_icon ) : string {
    if( empty( $value ) ) {
        return '';
    }

    return
        "<div class='d-flex small text-muted'>
            <span class='me-3'><i class='fas $fa_icon'></i></span>
            <span>$value</span>
        </div>";
}

/**
 * Creeert een container met de info over de activiteit:
 *  - Inschrijvingsinfo
 *  - Locatieinfo
 *  - Contactinfo
 * Dit zal worden gerendert als de activiteit nog niet heeft plaats gevonden. 
 */
function pre_activiteit_container_func() : string {
    $activiteit_info_container = '';
    $activiteit_datum = get_field( 'activiteit_datum' );
    // Enkel renderen als evenement nog niet is geweest
    if( $activiteit_datum != NULL && date( 'Ymd' ) <= $activiteit_datum ) {
        if( get_field( 'inschrijvingsinfo_activiteit_heeftInschrijvingsinfo' ) ) {
            $activiteit_info_container .= _create_inschrijvings_card();
        }

        if( get_field( 'locatieinfo_activiteit_heeftLocatieinfo' ) ) {
            $activiteit_info_container .= _create_locatie_card();
        }

        if( get_field( 'contactpersoon_activiteit_heeftContactpersoon' ) ) { 
            $activiteit_info_container .= _create_contact_card();
        }
    }

    if( empty( $activiteit_info_container ) ) {
        return '';
    }

    return
        "<div class='activiteit-info-container'>
            $activiteit_info_container
        </div>";
}
add_shortcode( 'pre_activiteit_container', 'pre_activiteit_container_func' );

function _create_inschrijvings_card() : string {
    $container_content = '';
    
    $heeft_uiterste_datum = get_field( 'inschrijvingsinfo_activiteit_heeftUitersteInschrijfdatum' );
    if( $heeft_uiterste_datum ) {
        $uiterste_datum = get_field( 'inschrijvingsinfo_activiteit_uitersteInschrijfdatum' );
        $date = DateTime::createFromFormat( 'Ymd', $uiterste_datum )->getTimestamp();
        // Formatteer datum als: dag in cijfers en maand voluitgeschreven
        $formatter = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, 'd MMMM' );
        $formatted_uiterste_datum = $formatter->format( $date );
    }

    $inschrijvings_info = '';
    $inschrijving_verplicht = get_field( 'inschrijvingsinfo_activiteit_heeftVerplichtInschrijven' );
    if( $heeft_uiterste_datum && $inschrijving_verplicht ) {
        $inschrijvings_info = "Inschrijven is verplicht en kan tot en met $formatted_uiterste_datum.";
    } elseif( $heeft_uiterste_datum ) {
        $inschrijvings_info = "Inschrijven kan tot en met $formatted_uiterste_datum.";
    } elseif( $inschrijving_verplicht ) {
        $inschrijvings_info = 'Om deel te nemen is inschrijven verplicht.';
    }
    
    if( !empty( $inschrijvings_info ) ) {
        $container_content .= "<div>$inschrijvings_info</div>";
    }

    $inschrijving_extra_info = get_field( 'inschrijvingsinfo_activiteit_extraInfotekst' );
    if( !empty( $inschrijving_extra_info ) ) {
        $container_content .= "<div>$inschrijving_extra_info</div>";
    }

    $inschrijving_link = get_field( 'inschrijvingsinfo_activiteit_inschrijvingslink' );
    $container_content .=
        "<div class='actie-knop'>
            <a href='$inschrijving_link' target='_blank'>Inschrijven</a>
        </div>";

    return _create_info_card( 'Inschrijven', 'fa-user-plus', $container_content );
}

function _create_locatie_card() : string {
    $adres = get_field( 'locatieinfo_activiteit_adres' );
    return _create_info_card( 'Locatie', 'fa-map-marker', $adres );
}

function _create_contact_card() : string {
    $naam =  _get_naam( 'contactpersoon_activiteit_contactpersoon' );
    $mail = _get_mail( 'contactpersoon_activiteit_contactpersoon' );

    $contact_type = get_post_type( get_field( 'contactpersoon_activiteit_contactpersoon' ) );
    if( $contact_type == 'werkgroep' || $contact_type == 'clubfunctie' ) {
        $functie = _get_functie( 'contactpersoon_activiteit_contactpersoon' );
        if( $contact_type == 'werkgroep' ) {
            $naam = 'de ' . strtolower( $functie ) . " ($naam)";
        } elseif( $contact_type == 'clubfunctie' ) {
            $naam = strtolower( $functie ) . " $naam";
        }
    }

    $container_content = "Voor meer info kan je contact opnemen met $naam.";

    $contact_content = empty( $mail ) ? '' : 
        "<div class='d-flex ms-1 mt-2'>
            <span class='me-3'><i class='fas fa-envelope'></i></span>
            <span><a href='mailto:$mail'>$mail</a></span>
        </div>";

    return _create_info_card( 'Contact', 'fa-question-circle', $container_content, $contact_content );
}

function _create_info_card( $titel, $titel_icoon, $content, $post_content = '' ) {
    return
        "<div class='activiteit-info-card'>
            <div class='titel'>
                <div class='icon'><i class='fas $titel_icoon'></i></div>
                <h2><strong>$titel</strong></h2>
            </div>
            <div class='content'>$content</div>
            $post_content
        </div>";
}

/** Post-Activiteit Shortcode **/
function post_activiteit_container_func() {
    if( get_post_type() == 'wedstrijdverslag' ) {
        $activiteit_id = get_field( 'wedstrijdverslag_wedstrijd' );
    } else {
        $activiteit_id = get_the_ID();
    }

    if( $activiteit_id == NULL ) {
        return '';
    }
    
    // Enkel wedstrijd is ondersteund op dit moment
    return '';
}
add_shortcode( 'post_activiteit_container', 'post_activiteit_container_func' );

/** Activiteitenlijst Shortcode **/
function activiteiten_container_func( $atts ) {
    $a = shortcode_atts( array(
        'klasse'   => '', // wedstrijd of evenement
		'type'     => '', // (veld, piste, jeugd, jogging) of (algemeen, sportkamp, stage)
        'aantal'   => 5,
        'minimaal' => true,
        'titel'    => false
	), $atts );
    
    $activiteit_klasse = $a['klasse'];
    if( empty( $activiteit_klasse ) ) {
        $post_types_filter = array( 'wedstrijd', 'evenement' );
    } else {
        $post_types_filter = array( $activiteit_klasse );
    }

    $activiteit_type = $a['type'];
    if( empty( $activiteit_type ) ) {
        $activiteit_types_filter = array();
    } else {
        if( empty( !$activiteit_klasse ) ) {
            $activiteit_types_filter = array( $activiteit_klasse . '_' . $activiteit_type );
        } else {
            $activiteit_types_filter = array( 
                'wedstrijd_' . $activiteit_type,
                'evenement_' . $activiteit_type
            );
        }
    }

    $titel = $a['titel'];

    $volgende_activiteiten = _get_activiteiten( $post_types_filter, $activiteit_types_filter, $a['aantal'] );
    $minimaal = $a['minimaal'];
    switch( $activiteit_klasse ) {
        case 'evenement':
            $activiteiten_container = _create_evenementen_container( $volgende_activiteiten, $activiteit_type, $minimaal, $titel );
            break;
        case 'wedstrijd':
            $activiteiten_container = _create_wedstrijden_container( $volgende_activiteiten, $activiteit_type, $minimaal, $titel );
            break;
        default:
            $hoofdtitel_label = $titel ? _create_hoofdtitel_activiteiten_container() : '';
            $activiteiten_container = _create_activiteiten_container( $volgende_activiteiten, $minimaal, 'activiteiten', $hoofdtitel_label );
            break;
    }

    return $activiteiten_container;
}
add_shortcode( 'activiteiten_container', 'activiteiten_container_func' );

function _get_activiteiten( array $post_types_filter, array $activiteit_types_filter, int $aantal = 5 ) : array {
    $volgende_activiteiten = array();

    $activiteit_args = _get_activiteiten_args( $post_types_filter, $activiteit_types_filter, $aantal );
    $activiteit_query = new WP_Query( $activiteit_args );
    if( $activiteit_query->have_posts() ) {
        while( $activiteit_query->have_posts() ) {
            $activiteit_query->the_post();
            // wedstrijd_type of evenement_type
            $activiteit_type_waarde = get_field( get_post_type() . '_type' );
            $activiteit_type_naam = $activiteit_type_waarde->name;
            $activiteit_type_slug = $activiteit_type_waarde->slug;
            $activiteit = array(
                'typeSlug'         => $activiteit_type_slug,
                'typeNaam'         => $activiteit_type_naam,
                'titel'            => get_field( 'activiteit_titel' ),
                'datum'            => get_field( 'activiteit_datum' ),
                'tijd'             => get_field( 'activiteit_tijd' ),
                'link'             => get_permalink(),
                'alternatieveLink' => get_field( 'activiteit_alternatieveLink' ),
                'samenvatting'     => get_the_excerpt()
            );
            array_push( $volgende_activiteiten, $activiteit );
        }
    }
    wp_reset_postdata();
    return $volgende_activiteiten;
}

function _get_activiteiten_args( array $post_types_filter, array $activiteit_types_filter, int $aantal ) : array {
    $activiteit_args = array(
        'post_type'      => $post_types_filter,
        'meta_query'     => array(
            'eigen__query'    => array(
                'key'           => 'activiteit_eigenOrganisatie',
                'value'         => 1
            ),
            'datum__order_by' => array(
                'key'           => 'activiteit_datum',
                'value'         => date( 'Ymd' ),
                'compare'       => '>='
            )
        ),
        'posts_per_page' => $aantal,
        'orderby'        => 'datum__order_by',
        'order'          => 'ASC'
    );

    if( !empty( $activiteit_types_filter ) ) {
        $tax_query = array(
            'relation' => 'OR',
            array(
                'taxonomy'  => 'type_evenement',
                'terms'     => $activiteit_types_filter,
                'field'     => 'slug',
                'operator'  => 'IN'
            ),
            array(
                'taxonomy'  => 'type_wedstrijd',
                'terms'     => $activiteit_types_filter,
                'field'     => 'slug',
                'operator'  => 'IN'
            ),
        );
        $activiteit_args['tax_query'] = $tax_query;
    }

    return $activiteit_args;
}

function _create_hoofdtitel_activiteiten_container() : string {
    return 
        "<div class='d-flex small text-muted'>
            <h2><span class='me-3 strong'><i class='fas fa-running'></i></span></h2>
            <h2><strong>Volgende activiteiten</strong></h2>
        </div>";
}

function _create_activiteiten_container( array $volgende_activiteiten, bool $minimaal, string $multiple_label, string $hoofdtitel ) {
    if( empty( $volgende_activiteiten ) ) {
        if( !empty( $hoofdtitel ) ) {
            return '';
        } else {
            return 
                "<div class='activiteit-container'>
                    <p class='no-event'>
                        Er zijn momenteel geen $multiple_label gepland...
                    </p>
                </div>";
        }
    }

    $activiteit_container = '';
    foreach( $volgende_activiteiten as $activiteit ) {
        $type_slug         = $activiteit['typeSlug'];
        $type_naam         = $activiteit['typeNaam'];
        $titel             = $activiteit['titel'];
        $datum             = _format_activiteit_datum( $activiteit['datum'], $minimaal );
        $tijd              = $activiteit['tijd'];
        $link              = $activiteit['link'];
        $alternatieve_link = $activiteit['alternatieveLink'];
        $samenvatting      = $activiteit['samenvatting'];

        $slug = explode( '_', $type_slug );
        $activiteit_klasse = $slug[0];
        $activiteit_type   = $slug[1];

        $activiteit_url = empty( $alternatieve_link ) ? $link : $alternatieve_link;

        $samenvatting = $minimaal ? '' : "<p class='samenvatting'>$samenvatting</p>";
        $type_label = ucfirst( $type_naam );

        if( $minimaal ) {
            $body = 
                "<div class='activiteit-header'>
                    <div class='type'>$type_label</div>
                    <div class='datum'>$datum</div>
                </div>
                <div class='activiteit-body'>
                    <p class='titel'>$titel</p>
                    $samenvatting
                </div>";
        } else {
            $body = 
                "<div class='activiteit-body'>
                    <p class='titel'>$titel</p>
                    $samenvatting
                </div>
                <div class='activiteit-header'>
                    <div class='type'>$type_label</div>
                    <div class='datum'>$datum</div>
                </div>";
        }

        $activiteit_container .= 
            "<div class='activiteit-blok'>
                <a class='activiteit $activiteit_type $activiteit_klasse' href='$activiteit_url'>
                    <div class='activiteit-info'>
                        $body
                    </div>
                </a>
            </div>";
    }  

    $minimaal_class = $minimaal ? ' minimaal' : ' uitgebreid';
    return 
        "$hoofdtitel
        <div class='activiteiten-container $minimaal_class'>
            $activiteit_container
        </div>";
}

function _format_activiteit_datum( string $datum, bool $minimaal = false ) : string {
    $datum = DateTime::createFromFormat( 'Ymd', $datum )->getTimestamp();
    $date_format = $minimaal ? 'd MMM' : 'd MMMM yyyy';
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, $date_format );
    return $format->format( $datum );
}  

// Dynamically set default value for acf "activiteit_eigenOrganisatie" field
add_filter('acf/load_field/name=activiteit_eigenOrganisatie', function( $field ) {
    $field['default_value'] = (get_post_type() == 'evenement');
    return $field;
});

?>