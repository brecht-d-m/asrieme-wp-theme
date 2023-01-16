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
        'veld'   => '',
        'functie' => '' ), $atts );
    return _get_naam( $a['veld'], $a['functie'] );
}
add_shortcode( 'lid_naam', 'lid_naam_func' );

function _get_naam( $veld, $functie ) {
    if ( ! empty( $functie ) ) {
        return _get_naam_clubfunctie( $functie );
    }

    if ( ! empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie ) ) {
        $lid_id = get_field( 'pagina_contact' );
        if ( empty ( $lid_id )) {
            return '';
        }
    }
    
    $post_type = get_post_type( $lid_id );
    if ( $post_type == 'clubfunctie' ) {
        $functie = get_field( 'clubfunctie_slugNaam', $lid_id );
        return _get_naam_clubfunctie( $functie );
    } elseif ( $post_type == 'lid' || $post_type == 'trainer' ) {
        return get_the_title( $lid_id );
    }
    return '';
}

function _get_naam_clubfunctie( $functie ) {
    $naam = '';
    $args = array(
        'post_type'      => 'clubfunctie', 
        'posts_per_page' => 1,
        'meta_key'       => 'clubfunctie_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $query->the_post();
        $leden = get_field( 'clubfunctie_lid' );
        $leden_count = count( $leden );
        $count = 0;
        foreach ( $leden as $lid_id ) {
            $naam .= get_the_title( $lid_id );
            if ( $count != $leden_count && $leden_count > 1 ) {
                $naam .= ' & ';
                $count++;
            }
        }
    }
    wp_reset_postdata();
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
        'veld'   => '',
        'functie' => '' ), $atts );
    return _get_mail( $a['veld'], $a['functie'] );
}
add_shortcode( 'lid_mail', 'lid_mail_func' );

function _get_mail( $veld, $functie ) {
    if ( ! empty( $functie ) ) {
        return _get_mail_clubfunctie( $functie );
    }

    if ( ! empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie ) ) {
        $lid_id = get_field( 'pagina_contact' );
    }

    if ( empty ($lid_id ) ) {
        return '';
    }
    
    $mail = '';
    $post_type = get_post_type( $lid_id );
    if ( $post_type == 'clubfunctie' ) {
        $mail = get_field( 'clubfunctie_mail', $lid_id );
    } else if ( $post_type == 'lid' ) {
        $mail = get_field( 'lid_mail', $lid_id );
    } else if ( $post_type == 'trainer' ) {
        $mail = get_field( 'trainer_mail', $lid_id );
    }
    return !empty ( $mail ) ? $mail : '' ;
}

function _get_mail_clubfunctie( $functie ) {
    $mail = '';
    $args = array(
        'post_type'      => 'clubfunctie', 
        'posts_per_page' => 1,
        'meta_key'       => 'clubfunctie_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $query->the_post();
        $mail = get_field( 'clubfunctie_mail' );
    }
    wp_reset_postdata();
    return !empty ( $mail ) ? $mail : '' ;
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
        'veld'         => '',
        'functie'      => '' ), $atts );

    $veld = $a['veld'];
    $functie = $a['functie'];
    if ( empty( $a['veld'] ) 
            && empty( $functie ) 
            && empty( get_field( 'pagina_contact' ) ) ) {
        return '';
    }

    $member_type = _get_member_type( $veld, $functie );
    $foto_aspect_ratio = $member_type == 'lid' ? 'square' : 'rectangle'; 
    $foto_vergroot = $member_type == 'lid' ? true : false;
    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( $foto_aspect_ratio );
    $card_properties->set_foto_vergroot( $foto_vergroot );
    $card_properties->set_card_relative_width( 'col-lg-12' );

    $member = new Member();
    $member->set_naam( _get_naam( $veld, $functie ) );
    $member->set_foto( _get_meta( $veld, $functie, 'fotoLink' ) );
    $member->set_functie( _get_functie( $veld, $functie ) );
    $functie_beschrijving = empty( get_field( 'pagina_contactInfo' ) ) ? '' : get_field( 'pagina_contactInfo' );
    $member->set_functie_beschrijving( $functie_beschrijving );
    $member->set_mail( _get_mail( $veld, $functie ) );
    $member->set_telefoon( _get_meta( $veld, $functie, 'telefoon' ) );
    $member->set_member_type( $member_type );
    return $member->create_member_card( $card_properties );
}
add_shortcode( 'lid_container', 'lid_container_func' );

function _get_member_type( $veld, $functie ) : string {
    if ( ! empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie )) {
        $lid_id = get_field( 'pagina_contact' );
    }

    if ( ! empty( $lid_id ) ) {
        $lid_post_type = get_post_type( $lid_id );
        if ( $lid_post_type != 'clubfunctie' ) {
            return $lid_post_type;
        } else {
            $leden = get_field( 'clubfunctie_lid', $lid_id );
            if ( count( $leden ) == 1 ) {
                return get_post_type( $leden[0] );
            } else {
                return '';
            }
        }
    }

    $functie_post_type = '';
    $args = array(
        'post_type'      => 'clubfunctie', 
        'posts_per_page' => 1,
        'meta_key'       => 'clubfunctie_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $query->the_post();
        $leden = get_field( 'clubfunctie_lid' );
        if ( count( $leden ) == 1 ) {
            $functie_post_type = get_post_type( $leden[0] );
        } else {
            $functie_post_type = '';
        }
    }
    wp_reset_postdata();
    return $functie_post_type;
}

function _get_functie( $veld, $functie ) : string {
    if ( ! empty( $functie ) ) {
        return _get_titel_clubfunctie( $functie );
    }

    if ( ! empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie )) {
        $lid_id = get_field( 'pagina_contact' );
    }

    if ( empty( $lid_id ) ) {
        return '';
    }
    
    $value = '';
    $post_type = get_post_type( $lid_id );
    if ( $post_type == 'clubfunctie' ) {
        $value = get_the_title( $lid_id );
    }
    return !empty( $value ) ? $value : '';
}

// Helperfunctie om de titel van een clubfunctie te weten te komen
// Input is de slugnaam van de functie
function _get_titel_clubfunctie( $functie ) {
    $functie_naam = '';
    $args = array(
        'post_type'      => 'clubfunctie', 
        'posts_per_page' => 1,
        'meta_key'       => 'clubfunctie_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $query->the_post();
        $functie_naam = get_the_title();
    }
    wp_reset_postdata();
    return $functie_naam;
}

function _get_meta( $veld, $functie, $key ) {
    if ( ! empty( $functie ) ) {
        $value = _get_meta_value_clubfunctie( $functie, $key );
        return ! empty( $value ) ? $value : '';
    }

    if ( ! empty( $veld ) ) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie )) {
        $lid_id = get_field( 'pagina_contact' );
    }
    
    if ( empty( $lid_id ) ) {
        return '';
    }

    $value = '';
    $post_type = get_post_type( $lid_id );
    if ( $post_type == 'clubfunctie' ) {
        $functie = get_field( 'clubfunctie_slugNaam', $lid_id );
        $value = _get_meta_value_clubfunctie( $functie, $key );
    } else if ( $post_type == 'lid' ) {
        $value = get_field( 'lid_'.$key, $lid_id );
    } else if ( $post_type == 'trainer' ) {
        $value = get_field( 'trainer_'.$key, $lid_id );
    }
    return ! empty( $value ) ? $value : '';
}

// Helperfunctie om een metavalue van een een lid van een clubfunctie 
// te weten te komen
// Input is de slugnaam van de functie en de meta key.
function _get_meta_value_clubfunctie( $functie, $key ) {
    $meta = '';
    $args = array(
        'post_type'      => 'clubfunctie', 
        'posts_per_page' => 1,
        'meta_key'       => 'clubfunctie_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $query->the_post();
        $leden = get_field( 'clubfunctie_lid' );
        $count = count( $leden );
        if ( $count == 0 || $count > 1 ) {
            $meta = '';
        } else {
            $post_type = get_post_type( $leden[0] );
            if ( $post_type == 'lid' ) {
                $meta = get_field( 'lid_'.$key, $leden[0] );
            } else if ( $post_type == 'trainer' ) {
                $meta = get_field( 'trainer_'.$key, $leden[0] );
            }
        }
    }
    wp_reset_postdata();
    return $meta;
}

?>