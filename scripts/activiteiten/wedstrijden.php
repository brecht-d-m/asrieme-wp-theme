<?php

/** Wedstrijden Container **/
function create_wedstrijden_container( $volgende_wedstrijden, $wedstrijd_type, $minimaal ) {
    $type_mapping = array(
        'jeugd'   => 'jeugdwedstrijden',
        'veld'    => 'veldlopen',
        'piste'   => 'pistewedstrijden',
        'jogging' => 'joggings'
    );
    $multiple_label = array_key_exists( $wedstrijd_type, $type_mapping ) ? $type_mapping[$wedstrijd_type] : 'wedstrijden';
    return create_activiteiten_container( $volgende_wedstrijden, $minimaal, $multiple_label );
}

/** Post-Wedstrijden Container**/
function post_wedstrijd_container( $wedstrijd_id ) {
    $post_wedstrijd_container = '';
    if ( $wedstrijd_id != NULL ) {
        $post_wedstrijd_container .= _create_resultaat_container( $wedstrijd_id );
        $post_wedstrijd_container .= _create_verslag_container( $wedstrijd_id );
        $post_wedstrijd_container .= _create_album_container( $wedstrijd_id );
    }

    return
        "<div class='activiteit-info-container'>
            $post_wedstrijd_container
        </div>";
}

function _create_resultaat_container( $wedstrijd_id ) {
    $uitslagen_links = _create_info_listing( 'wedstrijduitslag', $wedstrijd_id );
    if ( empty( $uitslagen_links ) ) {
        return '';
    }
    $container_content = "<ul>$uitslagen_links</ul>";
    return _create_info_container( 'Uitslag', 'fa-trophy', $container_content );
}

function _create_verslag_container( $wedstrijd_id ) {
    $verslagen_links = _create_info_listing( 'wedstrijdverslag', $wedstrijd_id );
    if ( empty( $verslagen_links ) ) {
        return '';
    }
    $container_content = "<ul>$verslagen_links</ul>";
    return _create_info_container( 'Verslagen', 'fa-pen', $container_content );
}

function _create_album_container( $wedstrijd_id ) {
    $album_links = _create_info_listing( 'wedstrijdalbum', $wedstrijd_id );
    if ( empty( $album_links ) ) {
        return '';
    }
    $container_content = "<ul>$album_links</ul>";
    return _create_info_container( 'Fotoalbums', 'fa-images', $container_content );
}

function _create_info_listing( $post_type, $wedstrijd_id ) {
    $wedstrijd_args = array(
        'posts_per_page' => -1,
        'post_type'      => $post_type,
        'meta_key'       => $post_type.'_wedstrijd',
        'meta_value'     => $wedstrijd_id
    );

    $post_links = '';
    $post_count = 0;
    $posts = new WP_Query( $wedstrijd_args );
    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) {
            $post_count++;
            $posts->the_post();

            $post_title = get_the_title();
            $post_url = get_field( $post_type.'_link' );
            if ( empty( $post_url ) ) {
                $post_url = get_the_permalink();
            }

            $post_links .= "<li><a href='$post_url'>$post_title</a></li>";
        }
    }
    wp_reset_postdata();
    return $post_links;
}

function laatste_verslagen_container_func () {
    return laatste_posts ( 'wedstrijdverslag', 'Laatste verslagen' );
}
add_shortcode( 'laatste_verslagen_container', 'laatste_verslagen_container_func' );

function laatste_uitslagen_container_func () {
    $huidig_jaar = date( 'Y' );
    $vorig_jaar = $huidig_jaar - 1;
    $date_filter = strtotime( '01-01-'.$vorig_jaar );
    $date_filter = date( 'Y-m-d', $date_filter );

    $args = array(
        'post_type'      => 'wedstrijduitslag',
        'posts_per_page' => -1,
        'date_query'     => array(
            'after'      => $date_filter
        )
    );
    
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $uitslag_map = array();
        while ( $query->have_posts() ) {
            $query->the_post();
            $uitslag = _create_uitslag_array();
            $jaar = $uitslag['jaar'];
            if ( !array_key_exists( $jaar, $uitslag_map )) {
                $uitslag_map[$jaar] = array();
            }
            array_push( $uitslag_map[$jaar], $uitslag );
        }
        wp_reset_postdata();
        return _create_uitslagen_tabellen( $uitslag_map );
    } else {
        wp_reset_postdata();
        return 
            "<div class='uitslagen-container'>
                <p class='no-uitslagen'>Er zijn geen uitslagen beschikbaar op dit moment...</p>
            </div>";
    }
}
add_shortcode( 'laatste_uitslagen_container', 'laatste_uitslagen_container_func' );

function _create_uitslag_array() {
    $wedstrijd_id = get_field( 'wedstrijduitslag_wedstrijd' );
    $uitslag_datum = get_field( 'activiteit_datum', $wedstrijd_id );
    $datum = DateTime::createFromFormat( 'Ymd', $uitslag_datum );
    $jaar = $datum->format( 'Y' );
    $datum_timestamp = $datum->getTimestamp();
    $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Brussels', IntlDateFormatter::GREGORIAN,'dd/MM' );
    $formatted_datum = $format->format( $datum );
    return array(
        'jaar'      => $jaar,
        'datum'     => $formatted_datum,
        'titel'     => get_the_title(),
        'wedstrijd' => $wedstrijd_id,
        'link'      => get_field( 'wedstrijduitslag_link' )
    );
}

function _create_uitslagen_tabellen( $uitslagen_mapping ) {
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
                <table class='uitslagen-tabel'>
                    <thead>
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
            </div>";
    }

    return 
        "<div class='uitslagen-container'>
            $uitslag_container
        </div>";
}

?>