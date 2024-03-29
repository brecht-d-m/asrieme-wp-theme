<?php

function _create_wedstrijd_suffix_infobar() : string {
    $meta_knoppen = '';
    $meta_knoppen .= _create_resultaat_card( get_the_ID() );
    $meta_knoppen .= _create_album_card( get_the_ID() );

    if( empty( $meta_knoppen ) ) {
        return '';
    }

    return 
        "<div class='d-none d-sm-block'>
            <div class='d-flex mt-4 overflow-scroll'>
                $meta_knoppen
            </div>
        </div>";
}

/** Wedstrijden Container **/
function _create_wedstrijden_container( $volgende_wedstrijden, $wedstrijd_type, $minimaal, $titel ) : string {
    $hoofdtitel_label = $titel ? _create_hoofdtitel_wedstrijden_container( $wedstrijd_type ) : '';
    $multiple_label = _get_wedstrijd_label( $wedstrijd_type );
    return _create_activiteiten_container( $volgende_wedstrijden, $minimaal, $multiple_label, $hoofdtitel_label );
}

function _create_hoofdtitel_wedstrijden_container( string $wedstrijd_type ) : string {
    $label = _get_wedstrijd_label( $wedstrijd_type );
    return 
        "<div class='d-flex small text-muted'>
            <h2><span class='me-3 strong'><i class='fas fa-running'></i></span></h2>
            <h2><strong>Volgende $label</strong></h2>
        </div>";
}

function _get_wedstrijd_label( $wedstrijd_type ) : string {
    switch( $wedstrijd_type ) {
        case 'jeugd': 
            $label = 'jeugdwedstrijden';
            break;
        case 'veld': 
            $label = 'veldlopen';
            break;
        case 'piste':
            $label = 'pistewedstrijden';
            break;
        case 'jogging':
            $label = 'joggings';
            break;
        default:
            $label = 'wedstrijden';
            break;
    }
    return $label;
}

/** Post-Wedstrijden Container**/
function _create_resultaat_card( $wedstrijd_id ) : string {
    $uitslagen_links = _create_link_array( 'wedstrijduitslag', 'titel', $wedstrijd_id );
    if( empty( $uitslagen_links ) ) {
        return '';
    }

    $knoppen = '';
    foreach( $uitslagen_links as $l ) {
        $titel = $l['titel'];
        $link = $l['link'];
        $knoppen .= 
            "<div class='activiteit-knop d-flex my-1 mx-1'>
                <a href='$link' class='text-truncate'>
                    <div class='icon me-2'><i class='fas fa-trophy'></i></div>
                    <div class='titel small'>Uitslag - $titel</div>
                </a>
            </div>";
    }

    return $knoppen;
}

function _create_album_card( $wedstrijd_id ) : string {
    $uitslagen_links = _create_link_array( 'wedstrijdalbum', 'fotograaf', $wedstrijd_id );
    if( empty( $uitslagen_links ) ) {
        return '';
    }

    $knoppen = '';
    foreach( $uitslagen_links as $l ) {
        $titel = $l['titel'];
        $link = $l['link'];
        $knoppen .= 
            "<div class='activiteit-knop d-flex my-1 mx-1'>
                <a href='$link' class='text-truncate'>
                    <div class='icon me-2'><i class='fas fa-camera'></i></div>
                    <div class='titel small'>Fotos - $titel</div>
                </a>
            </div>";
    }

    return $knoppen;
}

function _create_link_array( string $post_type, string $key_titel, int $wedstrijd_id ) : array {
    if( $wedstrijd_id == NULL ) {
        return array();
    }

    $wedstrijd_args = array(
        'posts_per_page' => -1,
        'post_type'      => $post_type,
        'meta_key'       => $post_type . '_wedstrijd',
        'meta_value'     => $wedstrijd_id
    );

    $post_links = array();
    $posts = new WP_Query( $wedstrijd_args );
    if( $posts->have_posts() ) {
        while( $posts->have_posts() ) {
            $posts->the_post();

            $post_titel = get_field( $post_type . '_' . $key_titel );
            $post_url = get_field( $post_type . '_link' );

            array_push( $post_links, array( 
                'link' => $post_url,
                'titel' => $post_titel
            ) );
        }
    }
    wp_reset_postdata();
    return $post_links;
}

function laatste_verslagen_container_func() : string {
    return laatste_posts( 'wedstrijdverslag', 'Laatste verslagen' );
}
add_shortcode( 'laatste_verslagen_container', 'laatste_verslagen_container_func' );

function laatste_uitslagen_container_func() : string {
    $huidig_jaar = date( 'Y' );
    $vorig_jaar = $huidig_jaar - 1;
    $date_filter = strtotime( '01-01-' . $vorig_jaar );
    $date_filter = date( 'Y-m-d', $date_filter );

    $args = array(
        'post_type'      => 'wedstrijduitslag',
        'posts_per_page' => -1,
        'date_query'     => array(
            'after'      => $date_filter
        )
    );
    
    $uitslag_map = array();
    $query = new WP_Query( $args );
    if( $query->have_posts() ) {
        while( $query->have_posts() ) {
            $query->the_post();
            $wedstrijd_id = get_field( 'wedstrijduitslag_wedstrijd' );
            $eigen_organisatie = get_field( 'activiteit_eigenOrganisatie', $wedstrijd_id );
            if( $eigen_organisatie ) {
                $uitslag = _create_uitslag_array( $wedstrijd_id );
                $jaar = $uitslag['jaar'];
                if( !array_key_exists( $jaar, $uitslag_map ) ) {
                    $uitslag_map[$jaar] = array();
                }
                array_push( $uitslag_map[$jaar], $uitslag );
            }
        }
    }
    wp_reset_postdata();

    if( !empty( $uitslag_map ) ) {
        return _create_uitslagen_tabellen( $uitslag_map );
    } else {
        return 
            "<div class='uitslagen-container'>
                <p class='no-uitslagen'>Er zijn geen nog uitslagen beschikbaar op dit moment...</p>
            </div>";
    }
}
add_shortcode( 'laatste_uitslagen_container', 'laatste_uitslagen_container_func' );

function _create_uitslag_array( int $wedstrijd_id ) : array {
    $uitslag_datum = get_field( 'activiteit_datum', $wedstrijd_id );
    $datum = DateTime::createFromFormat( 'Ymd', $uitslag_datum );
    $jaar = $datum->format( 'Y' );
    $datum_timestamp = $datum->getTimestamp();
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN,'dd/MM' );
    $formatted_datum = $format->format( $datum );
    return array(
        'jaar'      => $jaar,
        'datum'     => $formatted_datum,
        'titel'     => get_the_title(),
        'wedstrijd' => $wedstrijd_id,
        'link'      => get_field( 'wedstrijduitslag_link' )
    );
}

function _create_uitslagen_tabellen( $uitslagen_mapping ) : string {
    $uitslag_container = '';
    foreach( $uitslagen_mapping as $jaar => $jaar_uitslagen_mapping ) {
        $uitslagen = '';
        foreach( $jaar_uitslagen_mapping as $uitslag ) {
            $datum = $uitslag['datum'];
            $titel = $uitslag['titel'];
            $link = $uitslag['link'];
            $wedstrijd = get_the_permalink( $uitslag['wedstrijd'] );

            $uitslagen .= 
                "<tr class='uitslag'>
                    <td class='datum'>$datum</td>
                    <td class='wedstrijd'><a href='$wedstrijd'>$titel</a></td>
                    <td class='link'><a href='$link'>link</a></td>
                </tr>";
        }

        $uitslag_container .=
            "<div class='uitslagen-subcontainer'>
                <h2 class='uitslagen-subcontainer-titel'>Resultaten $jaar</h2>
                <div class='table-responsive'>
                    <table class='table uitslagen-tabel'>
                        <thead class='thead-light'>
                            <tr>
                                <th class='datum'>Datum</th>
                                <th class='wedstrijd'>Wedstrijd</th>
                                <th class='link'>Uitslag</th>
                            </tr>
                        </thead>
                        <tbody>
                            $uitslagen
                        </tbody>
                    </table>
                </div>
            </div>";
    }

    return 
        "<div class='uitslagen-container'>
            $uitslag_container
        </div>";
}

function gerelateerde_wedstrijd_button_func() : string {
    $wedstrijd_id = get_field( 'wedstrijdverslag_wedstrijd' );
    if( $wedstrijd_id == NULL ) {
        return '';
    }

    $eigen_organisatie = get_field( 'activiteit_eigenOrganisatie', $wedstrijd_id );
    if( ! $eigen_organisatie ) {
        return '';
    }

    $wedstrijd_link = get_permalink( $wedstrijd_id );
    return 
        "<div class='d-flex flex-row-reverse'>
            <div class='back-button-container gerelateerde-wedstrijd'>
                <a href='$wedstrijd_link'>
                    <div class='icon'><i class='fas fa-chevron-left'></i></div>
                    <div class='titel'>Gerelateerde wedstrijd</div>
                </a>
            </div>
        </div>";
}
add_shortcode( 'gerelateerde_wedstrijd_button', 'gerelateerde_wedstrijd_button_func' );

?>