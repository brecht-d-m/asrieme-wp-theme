<?php 

/**
 * Creeert een container met de datum van de activiteit. 
 *
 * Dit kan opgeroepen worden via de [activiteit_datum_container] shortcode.
 * Om de activiteit te weten, wordt:
 *  - ofwel aangenomen dat de huidige post een wedstrijdverslag is (en dan 
 *    wordt wedstrijdverslag_wedstrijd genomen om aan  de id te geraken)
 *  - ofwel de huidige post een activiteit is
 */
function activiteit_datum_container_func() : string {
    if ( get_post_type() == 'wedstrijdverslag' ) {
        $activiteit_id = get_field( 'wedstrijdverslag_wedstrijd' );
        $activiteit_datum = get_field( 'activiteit_datum', $activiteit_id );
    } else {
        $activiteit_datum = get_field( 'activiteit_datum' );
    }
    
    if ( empty( $activiteit_datum )) {
        return '';
    }

    $activiteit_datum = _format_activiteit_datum( $activiteit_datum );
    return
        "<div class='activiteit value-wrapper'>
            <div class='icon'><i class='fas fa-calendar-alt'></i></div>
            <h2 class='value datum'>$activiteit_datum</h2>
        </div>";
}
add_shortcode( 'activiteit_datum_container', 'activiteit_datum_container_func' );

/**
 * Creeert een container met de tijd van de activiteit. 
 *
 * Dit kan opgeroepen worden via de [activiteit_tijd_container] shortcode.
 * Om de activiteit te weten, wordt:
 *  - ofwel aangenomen dat de huidige post een wedstrijdverslag is (en dan 
 *    wordt wedstrijdverslag_wedstrijd genomen om aan  de id te geraken)
 *  - ofwel de huidige post een activiteit is
 */
function activiteit_tijd_container_func() {
    if ( get_post_type() == 'wedstrijdverslag') {
        $activiteit_id = get_field( 'wedstrijdverslag_wedstrijd' );
        $activiteit_datum = get_field( 'activiteit_tijd', $activiteit_id );
    } else {
        $activiteit_tijd = get_field( 'activiteit_tijd' );
    }

    if ( empty( $activiteit_tijd )) {
        return '';
    }

    return
        "<div class='activiteit value-wrapper'>
            <div class='icon'><i class='fas fa-clock'></i></div>
            <h2 class='value tijd'>$activiteit_tijd</h2>
        </div>";
}
add_shortcode( 'activiteit_tijd_container', 'activiteit_tijd_container_func' );

/** Pre-Activiteit Shortcode **/
function pre_activiteit_container_func() {
    $activiteit_info_container = '';
    $activiteit_datum = get_field( 'activiteit_datum' );
    // Enkel renderen als evenement nog niet is geweest
    if ( date('Ymd') <= $activiteit_datum ) {
        if ( get_field( 'inschrijvingsinfo_activiteit_heeftInschrijvingsinfo' )) {
            $activiteit_info_container .= _create_inschrijvings_container();
        }

        if ( get_field( 'locatieinfo_activiteit_heeftLocatieinfo' )) {
            $activiteit_info_container .= _create_locatie_container();
        }

        if ( get_field( 'contactpersoon_activiteit_heeftContactpersoon' )) { 
            $activiteit_info_container .= _create_contact_container();
        }
    }

    if ( empty( $activiteit_info_container )) {
        return '';
    }

    return
        "<div class='activiteit-info-container'>
            $activiteit_info_container
        </div>";
}
add_shortcode( 'pre_activiteit_container', 'pre_activiteit_container_func' );

function _create_inschrijvings_container() {
    $container_content = '';
    
    $heeft_uiterste_datum = get_field( 'inschrijvingsinfo_activiteit_heeftUitersteInschrijfdatum' );
    if ( $heeft_uiterste_datum ) {
        $uiterste_datum = get_field( 'inschrijvingsinfo_activiteit_uitersteInschrijfdatum' );
        $date = DateTime::createFromFormat( 'Ymd', $uiterste_datum )->getTimestamp();
        $formatter = new IntlDateFormatter('nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Brussels', IntlDateFormatter::GREGORIAN, 'd MMMM');
        $formatted_uiterste_datum = $formatter->format($date);
    }

    $inschrijvings_info = '';
    $inschrijving_verplicht = get_field( 'inschrijvingsinfo_activiteit_heeftVerplichtInschrijven' );
    if ( $heeft_uiterste_datum && $inschrijving_verplicht ) {
        $inschrijvings_info = "Inschrijven is verplicht en kan tot en met $formatted_uiterste_datum.";
    } else if ( $heeft_uiterste_datum ) {
        $inschrijvings_info = "Inschrijven kan tot en met $formatted_uiterste_datum.";
    } else if ( $verplicht ) {
        $inschrijvings_info = 'Om deel te nemen is inschrijven verplicht.';
    }
    
    if( !empty( $inschrijvings_info ) ) {
        $container_content .= "<div>$inschrijvings_info</div>";
    }

    $inschrijving_extra_info = get_field( 'inschrijvingsinfo_activiteit_extraInfotekst' );
    if ( !empty( $inschrijving_extra_info )) {
        $container_content .= "<div>$inschrijving_extra_info</div>";
    }

    $inschrijving_link = get_field( 'inschrijvingsinfo_activiteit_inschrijvingslink' );
    $container_content .=
        "<div class='actie_knop'>
            <a href='$inschrijving_link' target='_blank'>Inschrijven</a>
        </div>";

    return _create_info_container( 'Inschrijven', 'fa-user-plus', $container_content );
}

function _create_locatie_container() {
    $adres = get_field( 'locatieinfo_activiteit_adres' );
    return _create_info_container( 'Locatie', 'fa-map-marker', $adres );
}

function _create_contact_container() {
    $contact = get_field( 'contactpersoon_activiteit_contactpersoon' );
    $naam = ($contact != NULL) ? get_field( 'lid_naam', $contact ) : '';
    $mail = ($contact != NULL) ? get_field( 'lid_email', $contact ) : '';

    $container_content = empty( $mail ) ? '' : " (<a href='mailto:$mail'>$mail</a>)";
    $container_content = "Voor meer info kan je contact opnemen met $naam$container_content.";
    return _create_info_container( 'Contact', 'fa-question-circle', $container_content );
}

function _create_info_container( $title, $title_icon, $content ) {
    return
        "<div class='activiteit-info-card'>
            <div class='title'>
                <div class='icon'><i class='fas $title_icon'></i></div>
                <h2>$title</h2>
            </div>
            <div class='content'>$content</div>
        </div>";
}

/** Post-Activiteit Shortcode **/
function post_activiteit_container_func() {
    if ( get_post_type() == 'wedstrijdverslag' ) {
        $activiteit_id = get_field( 'wedstrijdverslag_wedstrijd' );
    } else {
        $activiteit_id = get_the_ID();
    }
    
    // Enkel wedstrijd is ondersteund op dit moment
    return _post_wedstrijd_container( $activiteit_id );
}
add_shortcode( 'post_activiteit_container', 'post_activiteit_container_func' );

/** Activiteitenlijst Shortcode **/
function activiteiten_container_func( $atts ) {
    $a = shortcode_atts( array(
        'klasse'   => '', // wedstrijd of evenement
		'type'     => '', // (veld, piste, jeugd, jogging) of (algemeen, sportkamp, stage)
        'aantal'   => 5,
        'minimaal' => true
	), $atts );
    
    $activiteit_klasse = $a['klasse'];
    if ( empty( $activiteit_klasse )) {
        $post_types_filter = array ( 'wedstrijd', 'evenement' );
    } else {
        $post_types_filter = array ( $activiteit_klasse );
    }

    // TODO
    $activiteit_type = $a['type'];
    if ( empty ( $activiteit_type )) {
        $activiteit_types_filter = array();
    } else {
        if ( empty( !$activiteit_klasse )) {
            $activiteit_types_filter = array( $activiteit_klasse.'_'.$activiteit_type );
        } else {
            $activiteit_types_filter = array( 
                'wedstrijd_'.$activiteit_type,
                'evenement_'.$activiteit_type
            );
        }
    }

    $volgende_activiteiten = _get_activiteiten( $post_types_filter, $activiteit_types_filter, $a['aantal'] );
    $minimaal = $a['minimaal'];
    switch ( $activiteit_klasse ) {
        case 'evenement':
            $activiteiten_container = create_evenementen_container( $volgende_activiteiten, $activiteit_type, $minimaal );
            break;
        case 'wedstrijd':
            $activiteiten_container = create_wedstrijden_container( $volgende_activiteiten, $activiteit_type, $minimaal );
            break;
        default:
            $activiteiten_container = create_activiteiten_container( $volgende_activiteiten, $minimaal, 'activiteiten' );
            break;
    }

    return $activiteiten_container;
}
add_shortcode( 'activiteiten_container', 'activiteiten_container_func' );

function _get_activiteiten( array $post_types_filter, array $activiteit_types_filter, int $aantal = 5 ) : array {
    $volgende_activiteiten = array();

    $activiteit_args = _get_activiteiten_args( $post_types_filter, $activiteit_types_filter, $aantal );
    $activiteit_query = new WP_Query( $activiteit_args );
    if ( $activiteit_query->have_posts() ) {
        while ( $activiteit_query->have_posts() ) {
            $activiteit_query->the_post();
            // wedstrijd_type of evenement_type
            $activiteit_type_waarde = get_field( get_post_type().'_type' );
            $activiteit_type_naam = $activiteit_type_waarde->name;
            $activiteit_type_slug = $activiteit_type_waarde->slug;
            $activiteit = array(
                'typeSlug'     => $activiteit_type_slug,
                'typeNaam'     => $activiteit_type_naam,
                'titel'        => get_field( 'activiteit_titel' ),
                'datum'        => get_field( 'activiteit_datum' ),
                'tijd'         => get_field( 'activiteit_tijd' ),
                'link'         => get_permalink(),
                'samenvatting' => get_the_excerpt()
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

    if ( !empty( $activiteit_types_filter )) {
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

function create_activiteiten_container( array $volgende_activiteiten, bool $minimaal, string $multiple_label ) {
    if ( empty( $volgende_activiteiten )) {
        return 
            "<div class='activiteit-container'>
                <p class='no-event'>
                    Er zijn momenteel geen $multiple_label gepland...
                </p>
            </div>";
    }

    $activiteit_container = '';
    foreach ( $volgende_activiteiten as $activiteit ) {
        $type_slug    = $activiteit['typeSlug'];
        $type_naam    = $activiteit['typeNaam'];
        $titel        = $activiteit['titel'];
        $datum        = _format_activiteit_datum( $activiteit['datum'], $minimaal );
        $tijd         = $activiteit['tijd'];
        $link         = $activiteit['link'];
        $samenvatting = $activiteit['samenvatting'];

        $slug = explode( '_', $type_slug );
        $activiteit_klasse = $slug[0];
        $activiteit_type   = $slug[1];

        $samenvatting = $minimaal ? '' : "<p class='samenvatting'>$samenvatting</p>";
        $activiteit_container .= 
            "<div class='activiteit-blok'>
                <a class='activiteit $activiteit_type $activiteit_klasse' href='$link'>
                    <div class='activiteit-info'>
                        <div class='activiteit-header'>
                            <div class='datum'>$datum</div>
                            <div class='type'>$type_naam</div>
                        </div>
                        <div class='activiteit-body'>
                            <p class='titel'>$titel</p>
                            $samenvatting
                        </div>
                    </div>
                </a>
            </div>";
    }  

    $minimaal_class = $minimaal ? ' minimaal' : ' uitgebreid';
    return 
        "<div class='activiteiten-container $minimaal_class'>
            $activiteit_container
        </div>";
}

function _format_activiteit_datum( string $datum, bool $minimaal = false ) : string {
    $datum = DateTime::createFromFormat( 'Ymd', $datum )->getTimestamp();
    $date_format = $minimaal ? 'd MMM' : 'd MMMM yy';
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Brussels', IntlDateFormatter::GREGORIAN, $date_format );
    return $format->format( $datum );
}  

?>