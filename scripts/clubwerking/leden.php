<?php

require_once __DIR__ . '/../members/class-member.php';
require_once __DIR__ . '/../members/class-member-card-properties.php';

use Member\Member;
use Member\Member_Card_Properties;

/**
 * Geeft de naam terug van het lid of het lid van de functie. Er zijn 2
 * parameters
 *  - Veld: verwijst naar het veld dat het id van een post bevat. Deze
 *    id kan verwijzen naar ofwel een lid ofwel een clubfunctie.
 *    Dit veld is optioneel
 *  - Functie: dit is de slug naam van een clubfunctie.
 *    Dit veld is optioneel
 * Als we zien dat één van deze twee verwijst naar een clubfunctie, 
 * nemen we voor de naam, de leden die verwijzen naar clubfunctie_lid.
 * 
 * Als geen van beide is ingevuld, kijken we naar pagina_contact. Dit kan op
 * zijn beurt weer verwijzen naar een lid of naar een clubfunctie.
 * 
 * Als er meerdere leden verbonden zijn aan de clubfunctie, worden deze 
 * samengevoegd met een "&".
 * 
 * Volgorde van belang
 *  1. Functie
 *  2. Veld
 *  3. Pagina - Contact
 */
function lid_naam_func( $atts ) {
    $a = shortcode_atts( array(
        'veld'      => '',
        'functie'   => '',
        'werkgroep' => '' ), $atts );
    return _get_naam( $a['veld'], $a['functie'], $a['werkgroep'] );
}
add_shortcode( 'lid_naam', 'lid_naam_func' );

function _get_naam( string $veld, string $functie = '', string $werkgroep = '' ) : string {
    if( !empty( $functie ) ) {
        return _get_naam_slug( 'clubfunctie', $functie );
    } elseif( !empty( $werkgroep ) ) {
        return _get_naam_slug( 'werkgroep', $werkgroep );
    }

    if( !empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else {
        $lid_id = get_field( 'pagina_contact' );
    }

    if( empty( $lid_id ) ) {
        return '';
    }
    
    $post_type = get_post_type( $lid_id );
    if( $post_type == 'clubfunctie' || $post_type == 'werkgroep' ) {
        return _get_naam_id( $post_type, $lid_id );
    } elseif( $post_type == 'lid' || $post_type == 'trainer' ) {
        return get_the_title( $lid_id );
    }
    return '';
}

function _get_naam_id( string $post_type, int $lid_id ) : string {
    $leden_field = _get_leden_field_name( $post_type );
    $leden = get_field( $leden_field, $lid_id );
    return _join_names( $leden );
}

function _get_naam_slug( string $post_type, string $slug_naam ) : string {
    $naam = '';
    $args = array(
        'post_type'      => $post_type, 
        'posts_per_page' => 1,
        'meta_key'       => $post_type . '_slugNaam',
        'meta_value'     => $slug_naam
    );
    $query = new WP_Query( $args );
    if( $query->have_posts() ) {
        $query->the_post();
        $leden_field = _get_leden_field_name( $post_type );
        $leden = get_field( $leden_field );
        $naam = _join_names( $leden );
    }
    wp_reset_postdata();
    return $naam;
}

function _join_names( array $leden ) : string {
    $naam = '';
    $leden_count = count( $leden );
    $count = 1;
    foreach( $leden as $lid_id ) {
        $naam .= get_the_title( $lid_id );
        if( $count != $leden_count && $leden_count > 1 ) {
            $naam .= ' & ';
            $count++;
        }
    }
    return $naam;
}

/**
 * Geeft de naam terug van het lid of het lid van de functie. Er zijn 2
 * parameters
 *  - Veld: verwijst naar het veld dat het id van een post bevat. Deze
 *    id kan verwijzen naar ofwel een lid ofwel een clubfunctie.
 *    Dit veld is optioneel
 *  - Functie: dit is de slug naam van een clubfunctie.
 *    Dit veld is optioneel
 * Als we zien dat één van deze twee verwijst naar een clubfunctie, 
 * nemen we voor het mailadres, het mailadres van de clubfunctie.
 * 
 * Als geen van beide is ingevuld, kijken we naar pagina_contact. Dit kan op
 * zijn beurt weer verwijzen naar een lid of naar een clubfunctie.
 * 
 * Voor het mail adres wordt altijd gekeken naar de clubfunctie (als deze
 * wordt meegegeven op een manier)
 * 
 * Volgorde van belang
 *  1. Functie
 *  2. Veld
 *  3. Pagina - Contact
 */
function lid_mail_func( $atts ) {
    $a = shortcode_atts( array(
        'veld'      => '',
        'functie'   => '',
        'werkgroep' => '' ), $atts );
    $mail = _get_mail( $a['veld'], $a['functie'], $a['werkgroep'] ); 
    return "<a href='mailto:$mail'>$mail</a>";
}
add_shortcode( 'lid_mail', 'lid_mail_func' );

function _get_mail( $veld, string $functie = '', string $werkgroep = '' ) : string {
    if( !empty( $functie ) ) {
        return _get_mail_slug( 'clubfunctie', $functie );
    } elseif( !empty( $werkgroep) ) {
        return _get_mail_slug( 'werkgroep', $werkgroep );
    }

    if( !empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else {
        $lid_id = get_field( 'pagina_contact' );
    }

    if( empty( $lid_id ) ) {
        return '';
    }
    
    $mail = '';
    $post_type = get_post_type( $lid_id );
    $mail = get_field( $post_type . '_mail', $lid_id );
    return !empty( $mail ) ? $mail : '' ;
}

function _get_mail_slug( string $post_type, string $slug_naam ) : string {
    $mail = '';
    $args = array(
        'post_type'      => $post_type, 
        'posts_per_page' => 1,
        'meta_key'       => $post_type . '_slugNaam',
        'meta_value'     => $slug_naam
    );
    $query = new WP_Query( $args );
    if( $query->have_posts() ) {
        $query->the_post();
        $mail = get_field( $post_type . '_mail' );
    }
    wp_reset_postdata();
    return !empty( $mail ) ? $mail : '' ;
}

/**
 * Geeft een infocontainer terug van het lid of het lid van de functie. 
 * Er zijn 2 parameters
 *  - Veld: verwijst naar het veld dat het id van een post bevat. Deze
 *    id kan verwijzen naar ofwel een lid ofwel een clubfunctie.
 *    Dit veld is optioneel
 *  - Functie: dit is de slug naam van een clubfunctie.
 *    Dit veld is optioneel
 * Als we zien dat één van deze twee verwijst naar een clubfunctie, 
 * nemen we voor de info, de leden die verwijzen naar clubfunctie_lid.
 * 
 * Als geen van beide is ingevuld, kijken we naar pagina_contact. Dit kan op
 * zijn beurt weer verwijzen naar een lid of naar een clubfunctie.
 * 
 * Als er meerdere leden verbonden zijn aan de clubfunctie:
 *  - Wordt de foto leeg gelaten
 *  - Worden de namen aan elkaar gebracht met een "&"
 * 
 * Voor het mail adres wordt altijd gekeken naar de clubfunctie (als deze
 * wordt meegegeven op een manier)
 * 
 * Volgorde van belang
 *  1. Functie
 *  2. Veld
 *  3. Pagina - Contact
 */
function lid_container_func( $atts ) {
    $a = shortcode_atts( array(
        'veld'      => '',
        'functie'   => '',
        'werkgroep' => '' ), $atts );

    $veld = $a['veld'];
    $functie = $a['functie'];
    $werkgroep = $a['werkgroep'];
    if( empty( $a['veld'] ) 
            && empty( $functie ) 
            && empty( $werkgroep )
            && empty( get_field( 'pagina_contact' ) ) ) {
        return '';
    }

    $functie_beschrijving = empty( get_field( 'pagina_contactInfo' ) ) ? '' : get_field( 'pagina_contactInfo' );
    $member_type = _get_member_type( $veld, $functie, $werkgroep );

    $foto_aspect_ratio = $member_type == 'lid' ? 'square' : 'rectangle'; 
    $foto_vergroot = $member_type == 'lid' ? true : false;
    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( $foto_aspect_ratio );
    $card_properties->set_foto_vergroot( $foto_vergroot );
    $card_properties->set_card_relative_width( 'col-lg-12' );

    $member = new Member();
    $member->set_naam( _get_naam( $veld, $functie, $werkgroep ) );
    $member->set_foto( _get_meta( $veld, $functie, $werkgroep, 'fotoLink' ) );
    $member->set_functie( _get_functie( $veld, $functie, $werkgroep ) );
    $member->set_functie_beschrijving( $functie_beschrijving );
    $member->set_mail( _get_mail( $veld, $functie, $werkgroep ) );
    $member->set_telefoon( _get_meta( $veld, $functie, $werkgroep, 'telefoon' ) );
    $member->set_member_type( $member_type );
    return $member->create_member_card( $card_properties );
}
add_shortcode( 'lid_container', 'lid_container_func' );

function _get_member_type( string $veld, string $functie, string $werkgroep ) : string {
    if( !empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } elseif( empty( $functie ) && empty( $werkgroep ) ) {
        $lid_id = get_field( 'pagina_contact' );
    }

    if( !empty( $lid_id ) ) {
        $lid_post_type = get_post_type( $lid_id );
        if( $lid_post_type != 'clubfunctie' && $lid_post_type != 'werkgroep' ) {
            return $lid_post_type;
        } else {
            $leden_field = _get_leden_field_name( $lid_post_type );
            $leden = get_field( $leden_field, $lid_id );
            if( count( $leden ) == 1 ) {
                return get_post_type( $leden[0] );
            } else {
                return '';
            }
        }
    }

    if( empty( $functie ) && empty( $werkgroep ) ) {
        return '';
    }

    $post_type = empty( $werkgroep ) ? 'clubfunctie' : 'werkgroep';
    $args = array(
        'post_type'      => $post_type, 
        'posts_per_page' => 1,
        'meta_key'       => $post_type . '_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    $leden = array();
    if( $query->have_posts() ) {
        $query->the_post();
        $leden_field = _get_leden_field_name( $post_type );
        $leden = get_field( $leden_field );
    }
    wp_reset_postdata();
    
    if( count( $leden ) == 1 ) {
        return get_post_type( $leden[0] );
    } else {
        return '';
    }
}

function _get_functie( string $veld, string $functie = '', string $werkgroep = '' ) : string {
    if( !empty( $functie ) ) {
        return _get_titel_slug( 'clubfunctie', $functie );
    } elseif( !empty( $werkgroep ) ) {
        return _get_titel_slug( 'werkgroep', $werkgroep );
    }

    if( !empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else {
        $lid_id = get_field( 'pagina_contact' );
    }

    if( empty( $lid_id ) ) {
        return '';
    }
    
    $value = '';
    $post_type = get_post_type( $lid_id );
    if( $post_type == 'clubfunctie' || $post_type == 'werkgroep' ) {
        $value = get_the_title( $lid_id );
    }
    return !empty( $value ) ? $value : '';
}

// Helperfunctie om de titel van een clubfunctie te weten te komen
// Input is de slugnaam van de functie
function _get_titel_slug( string $post_type, string $slug ) : string {
    $slug_naam = '';
    $args = array(
        'post_type'      => $post_type, 
        'posts_per_page' => 1,
        'meta_key'       => $post_type . '_slugNaam',
        'meta_value'     => $slug
    );
    $query = new WP_Query( $args );
    if( $query->have_posts() ) {
        $query->the_post();
        $slug_naam = get_the_title();
    }
    wp_reset_postdata();
    return $slug_naam;
}

function _get_meta( string $veld, string $functie, string $werkgroep, string $key ) : string {
    if( !empty( $functie ) ) {
        $value = _get_meta_value_slug( 'clubfunctie', $key, $functie );
        return !empty( $value ) ? $value : '';
    } elseif( !empty( $werkgroep ) ) {
        $value = _get_meta_value_slug( 'werkgroep', $key, $werkgroep );
        return !empty( $value ) ? $value : '';
    }

    if( !empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else {
        $lid_id = get_field( 'pagina_contact' );
    }
    
    if( empty( $lid_id ) ) {
        return '';
    }

    $value = '';
    $post_type = get_post_type( $lid_id );
    if( $post_type == 'clubfunctie' || $post_type == 'werkgroep' ) {
        $value = _get_meta_value_id( $post_type, $key, $lid_id);
    } elseif( $post_type == 'lid' || $post_type == 'trainer' ) {
        $value = get_field( $post_type . '_' . $key, $lid_id );
    }
    return !empty( $value ) ? $value : '';
}

// Helperfunctie om een metavalue van een een lid van een clubfunctie 
// te weten te komen
// Input is de slugnaam van de functie en de meta key.
function _get_meta_value_slug( string $post_type, string $key, string $slug ) : string {
    $args = array(
        'post_type'      => $post_type, 
        'posts_per_page' => 1,
        'meta_key'       => $post_type . '_slugNaam',
        'meta_value'     => $slug
    );
    $query = new WP_Query( $args );
    $leden = array();
    if( $query->have_posts() ) {
        $query->the_post();
        $leden_field = _get_leden_field_name( $post_type );
        $leden = get_field( $leden_field );
    }
    wp_reset_postdata();
    
    return _parse_meta_value( $post_type, $key, $leden );
}

function _get_meta_value_id( string $post_type, string $key, int $lid_id ) : string {
    $leden_field = _get_leden_field_name( $post_type );
    $leden = get_field( $leden_field, $lid_id );
    return _parse_meta_value( $post_type, $key, $leden );
}

function _parse_meta_value( string $post_type, string $key, array $leden ) : string {
    $count = count( $leden );
    if( $count == 0 || $count > 1 ) {
        return '';
    } else {
        $post_type = get_post_type( $leden[0] );
        if( $post_type == 'lid' || $post_type == 'trainer' ) {
            $meta_value = get_field( $post_type . '_' . $key, $leden[0] );
            return !empty( $meta_value ) ? $meta_value : '';
        } else {
            return '';
        }
    }
}

function _get_leden_field_name( string $post_type ) : string {
    $leden_field = $post_type == 'clubfunctie' ? 'lid' : 'lead';
    return $post_type . '_' . $leden_field;
}

?>