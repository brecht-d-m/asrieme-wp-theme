<?php

require_once 'class-sponsor.php';
require_once 'class-sponsor-card-properties.php';

use Sponsor\Sponsor;
use Sponsor\Sponsor_Card_Properties;

function sponsor_willekeurig_func( $atts ) {
    $a = shortcode_atts( array( 'bordered' => false ), $atts );

    // Don't filter on sponsor type and return max 1 sponsor
    $query = new WP_Query( _get_sponsors_args( '', 1 ) );
    if( $query->have_posts() ) {
        $query->the_post();
        $sponsor = _create_sponsor( 'random' );
    }
    wp_reset_postdata();

    if( $sponsor == NULL ) {
        return '';
    }

    $card_properties = new Sponsor_Card_Properties();
    $card_properties->set_gebruik_extra_info( false );
    $card_properties->set_bordered( $a['bordered'] );
    $card_properties->set_card_relative_width( 'col-lg-12' );
    return $sponsor->create_sponsor_card( $card_properties );
}
add_shortcode( 'sponsor_willekeurig', 'sponsor_willekeurig_func' );

function sponsors_container_func( $atts ) {
    $a = shortcode_atts( array(
        'bordered' => false,
        'type' => '' ), $atts );

    $type = $a['type'];
    if( !empty( $type ) ) {
        $sponsor_type = 'sponsor_' . $type;
    } else {
        $sponsor_type = '';
    }

    $sponsors = array();
    $query = new WP_Query( _get_sponsors_args( $sponsor_type, -1 ) );
    if( $query->have_posts() ) {
        while( $query->have_posts() ) {
            $query->the_post();
            array_push( $sponsors, _create_sponsor( $type ) );
        }
    }
    wp_reset_postdata();

    if( empty( $sponsors ) ) {
        return '';
    }

    $card_properties = new Sponsor_Card_Properties();
    $card_properties->set_gebruik_extra_info( true );
    $card_properties->set_bordered( $a['bordered'] );
    $card_properties->set_card_relative_width( 'col-lg-4' );

    $sponsor_container = '';
    foreach( $sponsors as $s => $sponsor ) {
        $sponsor_container .= $sponsor->create_sponsor_card( $card_properties );
    }

    return 
        "<div class='sponsors-container row'>
            $sponsor_container
        </div>";
}
add_shortcode( 'sponsors_container', 'sponsors_container_func' );

function _get_sponsors_args( string $sponsor_type, int $aantal ) : array {
    $sponsor_args = array(
        'post_type'      => 'sponsor',
        'posts_per_page' => $aantal,
        'orderby'        => 'rand'
    );

    if( !empty( $sponsor_type ) ) {
        $tax_query = array(
            array(
                'taxonomy'  => 'type_sponsor',
                'terms'     => $sponsor_type,
                'field'     => 'slug',
                'operator'  => 'IN'
            )
        );
        $sponsor_args['tax_query'] = $tax_query;
    }

    return $sponsor_args;
}

function _create_sponsor( string $sponsor_type ) : Sponsor {
    $sponsor = new Sponsor();
    $sponsor->set_naam( get_the_title() );
    $sponsor->set_logo( get_field( 'sponsor_fotoLink' ) );
    $sponsor->set_link( get_field( 'sponsor_link' ) );
    $sponsor->set_slogan( get_field( 'sponsor_slogan' ) );
    $sponsor->set_type( $sponsor_type );
    return $sponsor;
}

?>