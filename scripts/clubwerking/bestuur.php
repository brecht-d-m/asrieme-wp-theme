<?php

require_once __DIR__ . '/../members/class-member.php';
require_once __DIR__ . '/../members/class-member-card-properties.php';
require_once 'class-werkgroep.php';

use Member\Member;
use Member\Member_Card_Properties;
use Werkgroep\Werkgroep;

function werkgroepen_container_func() {
    $werkgroepen_info = _get_werkgroepen();
    
    $werkgroepen_info_cards = '';
    foreach( $werkgroepen_info as $info ) {
        $werkgroepen_info_cards .= $info->create_werkgroep_card();
    }
    
    return 
        "<div class='werkgroepen-info-container row'>
            $werkgroepen_info_cards
        </div>";
}
add_shortcode( 'werkgroepen_container', 'werkgroepen_container_func' );

function werkgroepen_listing_func() {
    $werkgroepen_info = _get_werkgroepen();
    
    $werkgroep_oplijsting = '';
    foreach( $werkgroepen_info as $info ) {
        $groep_titel = $info->get_titel();
        $groep_naam = $info->get_naam();
        $werkgroep_oplijsting .= "<li><a href='#werkgroep-$groep_naam'>$groep_titel</a></li>";
    }
    return 
        "<ul class='werkgroep-listing'>
            $werkgroep_oplijsting
        </ul>";
}
add_shortcode( 'werkgroepen_listing', 'werkgroepen_listing_func' );

function _get_werkgroepen() : array {    
    $werkgroepen = array();
    $query = new WP_Query( _get_werkgroepen_args() );
    if( $query->have_posts() ) {
        while( $query->have_posts() ) {
            $query->the_post();
            array_push( $werkgroepen, _create_werkgroep() );
        }
    }
    wp_reset_postdata();
    
    return $werkgroepen;
}

function _get_werkgroepen_args() : array {
    return array(
        'post_type'         => 'werkgroep',
        'posts_per_page'    => -1,
        'meta_key'          => 'werkgroep_seqNo',
        'meta_query'        => array(
            'is_hoofdwerkgroep_query' => array(
                'key'    => 'werkgroep_isSubwerkgroep',
                'value'  => 0
            )
        ),
        'orderby'           => 'meta_key',
        'order'             => 'ASC'
    );
}

function _create_werkgroep() : Werkgroep {
    $leads = array();
    $werkgroep_leads = get_field( 'werkgroep_lead' );
    if( !empty( $werkgroep_leads ) ) {
        foreach( $werkgroep_leads as $lead_id ) {
            array_push( $leads, get_post( $lead_id ) );
        }
    }

    $subwerkgroepen = array();
    $werkgroep_subwerkgroepen = get_field( 'werkgroep_subwerkgroepen' );
    if( !empty( $werkgroep_subwerkgroepen ) ) {
        foreach( $werkgroep_subwerkgroepen as $subwerkgroep_id ) {
            array_push( $subwerkgroepen, get_post( $subwerkgroep_id ) );
        }
    }

    $werkgroep = new Werkgroep();
    $werkgroep->set_titel( get_the_title() );
    $werkgroep->set_naam( get_post()->post_name );
    $werkgroep->set_info( get_field( 'werkgroep_info' ) );
    $werkgroep->set_mail( get_field( 'werkgroep_mail' ) );
    $werkgroep->set_subwerkgroepen( $subwerkgroepen );
    $werkgroep->set_leads( $leads );
    return $werkgroep;
}

function bestuurders_container_func( $atts ) {
    $a = shortcode_atts( array( 'type'   => '' ), $atts );

    $bestuurders = array();
    $leden = new WP_Query( _get_bestuurders_args( $a['type'] ) );
    if( $leden->have_posts() ) {
        while( $leden->have_posts() ) {
            $leden->the_post();   
            array_push( $bestuurders, _create_bestuurder() );
        }
    }
    wp_reset_postdata();

    if( empty( $bestuurders ) ) {
        return '';
    }

    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( "square" );
    $card_properties->set_card_relative_width( "col-lg-6" );

    $bestuurders_container = '';
    foreach( $bestuurders as $b => $bestuurder ) {
        $bestuurders_container .= $bestuurder->create_member_card( $card_properties );
    }

    return 
        "<div class='bestuurders-container row'>
            $bestuurders_container
        </div>";
}
add_shortcode( 'bestuurders_container', 'bestuurders_container_func' );

function _get_bestuurders_args( string $bestuurder_type ) : array {
    return array(
        'post_type'       => 'lid',
        'posts_per_page'  => -1,
        'tax_query'      => array(
            array(
                'taxonomy'  => 'type_lid',
                'terms'     => 'bestuurder_' . $bestuurder_type,
                'field'     => 'slug',
                'operator'  => 'IN'
            )
        )
    );
}

function _create_bestuurder() : Member {
    $bestuurder = new Member();
    $bestuurder->set_naam( get_the_title() );
    $bestuurder->set_foto( get_field( 'lid_fotoLink' ) );
    return $bestuurder;
}

function bestuursfuncties_container_func() {
    $bestuursfuncties = array();
    $leden = new WP_Query( _get_bestuursfuncties_args() );
    if( $leden->have_posts() ) {
        while( $leden->have_posts() ) {
            $leden->the_post();
            $mail = get_field( 'clubfunctie_mail' );
            foreach( get_field( 'clubfunctie_lid' ) as $bestuurder_id ) {
                $bestuurder = _create_bestuurderfunctie( $mail, $bestuurder_id );
                array_push( $bestuursfuncties, $bestuurder );                
            }
        }
    }
    wp_reset_postdata();

    if( empty( $bestuursfuncties ) ) {
        return '';
    }

    // Herorder bestuursfuncties (zodat voorzitter/ondervoorzitter onder elkaar staat)
    $bestuursfuncties_reordered = array();
    $j = (int) floor( count( $bestuursfuncties ) / 2 );
    for( $i = 0; $i < ceil( count( $bestuursfuncties ) / 2 ); $i++ ) {
        $bestuursfuncties_reordered[$i*2] = $bestuursfuncties[$i];
        if( array_key_exists( $j+$i+1 , $bestuursfuncties) ) {
            $bestuursfuncties_reordered[$i*2+1] = $bestuursfuncties[$j+$i+1];
        }
    }

    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( 'square' );
    $card_properties->set_card_relative_width( 'col-lg-6' );

    $bestuursfuncties_container = '';
    foreach( $bestuursfuncties_reordered as $b => $bestuurder ) {
        $bestuursfuncties_container .= $bestuurder->create_member_card( $card_properties );
    }
    
    return 
        "<div class='bestuurders-container row'>
            $bestuursfuncties_container
        </div>";
}
add_shortcode( 'bestuursfuncties_container', 'bestuursfuncties_container_func' );

function _get_bestuursfuncties_args() : array {
    return array(
        'post_type'       => 'clubfunctie',
        'posts_per_page'  => -1,
        'tax_query'       => array(
            array(
                'taxonomy'  => 'type_clubfunctie',
                'terms'     => 'clubfunctie_bestuursfunctie',
                'field'     => 'slug',
                'operator'  => 'IN'
            )
        ),
        'meta_query'      => array(
            'seqno__order_by' => array(
                'key'           => 'clubfunctie_seqNo'
            )
        ),
        'orderby'         => 'seqno__order_by',
        'order'           => 'ASC'
    );
}

function _create_bestuurderfunctie( string $bestuursfunctie_mail, int $bestuurder_id ) : Member {
    $gender = get_field( 'lid_gender', $bestuurder_id );
    $functie = _get_correct_functie( get_the_title(), $gender );
    $bestuurder = new Member();
    $bestuurder->set_naam( get_the_title( $bestuurder_id ) );
    $bestuurder->set_foto( get_field( 'lid_fotoLink', $bestuurder_id ) );
    $bestuurder->set_functie( $functie );
    $bestuurder->set_mail( $bestuursfunctie_mail );
    $bestuurder->set_telefoon( get_field( 'lid_telefoon', $bestuurder_id ) );
    return $bestuurder;
}

function _get_correct_functie( $functie, $gender ) {
    if( $functie == 'Ondervoorzitter' ) {
        return $gender == 'm' ? $functie : 'Ondervoorzitster';
    }
    return $functie;
}

?>