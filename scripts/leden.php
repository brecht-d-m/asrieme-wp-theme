<?php

require_once 'members/class-member.php';
require_once 'members/class-member-card-properties.php';
require_once 'members/members.php';

use Member\Member;
use Member\Member_Card_Properties;
use function Member\generate_member_card;

function lid_naam_func( $atts ) {
    $a = shortcode_atts( array(
		'veld'   => '',
        'functie' => '',
	), $atts );
    return _get_naam( $a['veld'], $a['functie'] );
}
add_shortcode( 'lid_naam', 'lid_naam_func' );

function _get_naam( $veld, $functie ) {
    if ( !empty( $functie )) {
        return _get_naam_functie( $functie );
    }

    if ( !empty( $veld )) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie )) {
        $lid_id = get_field( 'pagina_contact' );
        if ( empty ( $lid_id )) {
            return '';
        }
    }
    
    $post_type = get_post_type( $lid_id );
    if ( $post_type == 'clubfunctie' ) {
        $functie = get_field( 'clubfunctie_slugNaam', $lid_id );
        return _get_naam_functie( $functie );
    } elseif ( $post_type == 'lid' ) {
        return get_the_title( $lid_id );
    }
    return '';
}

function _get_naam_functie( $functie ) {
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

function lid_mail_func( $atts ) {
    $a = shortcode_atts( array(
		'veld'   => '',
        'functie' => '',
	), $atts );
    return _get_mail( $a['veld'], $a['functie'] );
}
add_shortcode( 'lid_mail', 'lid_mail_func' );

function _get_mail( $veld, $functie ) {
    if ( !empty( $functie )) {
        return _get_mail_functie( $functie );
    }

    if ( !empty( $veld )) { 
        $lid_id = get_field( $veld );
    } else if ( empty( $veld ) && empty( $functie )) {
        $lid_id = get_field( 'pagina_contact' );
    }

    if ( empty ($lid_id )) {
        return '';
    }
    
    $mail = '';
    $post_type = get_post_type( $lid_id );
    if ( $post_type == 'clubfunctie' ) {
        $mail = get_field( 'clubfunctie_mail', $lid_id );
    } else if ( $post_type == 'lid' ) {
        $mail = get_field( 'lid_mail', $lid_id );
    }
    return !empty ( $mail ) ? $mail : '' ;
}

function _get_mail_functie( $functie ) {
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

function lid_container_func( $atts ) {
    $a = shortcode_atts( array(
		'veld'         => '',
        'functie'      => '',
	), $atts );

    if ( empty( $veld ) 
            && empty( $functie ) 
            && empty( get_field( 'pagina_contact' ) ) ) {
        return '';
    }

    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( 'square' );
    $card_properties->set_foto_vergroot( true );
    $card_properties->set_card_relative_width( 'col-lg-12' );

    $member = new Member();
    $member->set_naam( _get_naam( $a['veld'], $a['functie'] ) )
            ->set_foto( _get_meta( $a['veld'], $a['functie'], 'fotoLink' ) )
            ->set_functie( _get_functie( $a['veld'], $a['functie'] ) )
            ->set_functie_beschrijving( get_field( 'pagina_contactInfo' ) )
            ->set_mail( _get_mail( $a['veld'], $a['functie'] ) )
            ->set_telefoon( _get_meta( $a['veld'], $a['functie'], 'telefoon' ) );
    return generate_member_card( $member, $card_properties );
}
add_shortcode( 'lid_container', 'lid_container_func' );

function _get_functie( $veld, $functie ) {
    if ( !empty( $functie )) {
        return _get_functie_functie( $functie );
    }

    if ( !empty( $veld )) { 
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

function _get_functie_functie( $functie ) {
    $functie = '';
    $args = array(
        'post_type'      => 'clubfunctie', 
        'posts_per_page' => 1,
        'meta_key'       => 'clubfunctie_slugNaam',
        'meta_value'     => $functie
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $query->the_post();
        $functie = get_the_title();
    }
    wp_reset_postdata();
    return $functie;
}

function _get_meta( $veld, $functie, $key ) {
    if ( !empty( $functie )) {
        $value = _get_meta_functie( $functie, $key );
        return !empty( $value ) ? $value : '';
    }

    if ( !empty( $veld )) { 
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
        $value = _get_meta_functie( $functie, $key );
    } else if ( $post_type == 'lid' ) {
        $value = get_field( 'lid_'.$key, $lid_id );
    }
    return !empty( $value ) ? $value : '';
}

function _get_meta_functie( $functie, $key ) {
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
            $meta = get_field( 'lid_'.$key, $leden[0] );
        }
    }
    wp_reset_postdata();
    return $meta;
}

?>