<?php

require_once __DIR__ . '/../members/class-member.php';
require_once __DIR__ . '/../members/class-member-card-properties.php';
require_once __DIR__ . '/../members/members.php';

use Member\Member;
use Member\Member_Card_Properties;
use function Member\generate_member_card;

function werkgroepen_container_func() {
    $hoofdwerkgroepen_info = get_werkgroepen();
    
    $werkgroepen_info_container = '';
    foreach ( $hoofdwerkgroepen_info as $info ) {
        $werkgroepen_info_container .= create_werkgroep_container( $info );
    }
    
    return 
        "<div class='werkgroepen-info-container'>
            $werkgroepen_info_container
        </div>";
}
add_shortcode( 'werkgroepen_container', 'werkgroepen_container_func' );

function create_werkgroep_container( $info ) {
    $groep_id    = $info['id'];
    $groep_titel = $info['titel'];

    $groep_info     = create_info_part( $info );
    $groep_contact  = create_mail_part( $info );
    $groep_lead     = create_lead_part( $info );
    $subwerkgroepen = create_sub_containers( $info );

    return 
        "<div id='werkgroep-$groep_id' class='werkgroep-container'>
            <h3>$groep_titel</h3>
            <div class='werkgroep-info-wrapper'>
                $groep_info
                <div class='werkgroep-info'>
                    $groep_lead
                    $groep_contact
                    $subwerkgroepen
                </div>
            </div>
        </div>";   
}

function create_info_part( $info ) {
    $info = $info['info'];
    if ( empty($info )) {
        return '';
    }

    return 
        "<div class='info value-wrapper'>
            <div class='icon'><i class='fas fa-info-circle'></i></div>
            <div>$info</div>
        </div>";
}

function create_mail_part( $info ) {
    $mail = $info['mail'];
    if ( empty($mail )) {
        return '';
    }

    $exploded_mails = explode( "\n", $mail );
    $adressen = '';
    foreach ( $exploded_mails as $adres ) {
        $adressen .= "<div class='mail-adres'><a href='mailto:$adres'>$adres</a></div>";
    }

    return 
        "<div class='mail value-wrapper'>
            <div class='icon'><i class='fas fa-envelope'></i></div>
            <div>
                $adressen
            </div>
        </div>";
}

function create_lead_part( $info ) {
    $nm_leads = count( $info['leads'] );

    $lead_tekst = 'Wordt geleid door ';
    $counter = 1;
    foreach ( $info['leads'] as $lead ) {
        $lead_tekst .= get_the_title( $lead->ID );
        if ( $counter != $nm_leads ) {
            $lead_tekst .= " & ";
        }
        $counter++;
    }

    if ( $counter == 1 ) {
        return '';
    }
    
    return 
        "<div class='leads value-wrapper'>
            <div class='icon'><i class='fas fa-user'></i></div>
            <div>
                $lead_tekst
            </div>
        </div>";
}

function create_sub_containers( $info ) {
    $subwerkgroepen_container = '';
    if ( empty( $info['subwerkgroepen'] )) {
        return '';
    }

    foreach ( $info['subwerkgroepen'] as $subwerkgroep ) {
        $sub_id = $subwerkgroep->ID;

        $leads = array();
        foreach ( get_field( 'werkgroep_lead', $sub_id ) as $lead_id ) {
            array_push( $leads, get_post( $lead_id ));
        }

        $sub_groep_info = array(
            'titel' => get_the_title( $sub_id ),
            'leads' => $leads,
            'mail' => get_field( 'werkgroep_mail', $sub_id ),
        );
        $subwerkgroepen_container .= create_sub_container( $sub_groep_info );
    }

    return $subwerkgroepen_container;
}

function create_sub_container( $info ) {
    $sub_titel = $info[ 'titel' ];
    $sub_mail_container = create_mail_part( $info );
    $sub_lead_container = create_lead_part( $info );

    return 
        "<div class='sub-werkgroep'>
            <h4>$sub_titel</h4>
            <div class='sub-werkgroep-info'>
                $sub_lead_container
                $sub_mail_container
            </div>
        </div>";
}

function werkgroepen_listing_func() {
    $werkgroepen_info = get_werkgroepen();
    
    $werkgroep_oplijsting = '';
    foreach ( $werkgroepen_info as $info ) {
        $groep_titel = $info['titel'];
        $groep_id = $info['id'];
        $werkgroep_oplijsting .= "<li><a href='#werkgroep-$groep_id'>$groep_titel</a></li>";
    }
    return 
        "<ul class='werkgroep-listing'>
            $werkgroep_oplijsting
        </ul>";
}
add_shortcode( 'werkgroepen_listing', 'werkgroepen_listing_func' );

function get_werkgroepen() : array {
    $args = array(
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
    
    $werkgroepen = array();
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $leads = array();
            $werkgroep_leads = get_field( 'werkgroep_lead' );
            if ( !empty( $werkgroep_leads )) {
                foreach( get_field('werkgroep_lead') as $lead_id ) {
                    array_push( $leads, get_post( $lead_id ));
                }
            }

            $werkgroep_info = array(
                'titel'           => get_the_title(),
                'info'            => get_field('werkgroep_info'),
                'subwerkgroepen'  => get_field('werkgroep_subwerkgroepen'),
                'leads'           => $leads,
                'mail'            => get_field('werkgroep_mail'),
                'id'              => get_the_ID());
            array_push( $werkgroepen, $werkgroep_info );
        }
    }
    wp_reset_postdata();
    
    return $werkgroepen;
}

function bestuurders_container_func( $atts ) {
    $a = shortcode_atts( array(
		'type'   => '',
	), $atts );

    $args = array(
        'post_type'       => 'lid',
        'posts_per_page'  => -1,
        'tax_query'      => array(
            array(
                'taxonomy'  => 'type_lid',
                'terms'     => 'bestuurder_'.$a['type'],
                'field'     => 'slug',
                'operator'  => 'IN'
            )
        )
    );
    $bestuurders = '';
    $leden = new WP_Query( $args );
    if ( $leden->have_posts() ) {
        $card_properties = new Member_Card_Properties();
        $card_properties->set_foto_aspect_ratio("square");
        $card_properties->set_card_relative_width("col-lg-6");

        while ( $leden->have_posts() ) {
            $leden->the_post();   
            $bestuurders .= _create_bestuurder_card( get_the_ID(), $card_properties );
        }
    }
    wp_reset_postdata();
    return 
        "<div class='bestuurders-container row'>
            $bestuurders
        </div>";
}
add_shortcode( 'bestuurders_container', 'bestuurders_container_func' );

function _create_bestuurder_card( int $lid_id, Member_Card_Properties $card_properties, string $functie = '', string $mail = '') {
    $bestuurder = new Member();
    $bestuurder->set_naam( get_the_title( $lid_id ) );
    $bestuurder->set_foto( get_field( 'lid_fotoLink', $lid_id ) );
    $bestuurder->set_functie( $functie );
    $bestuurder->set_mail( $mail );
    $bestuurder->set_telefoon( get_field( 'lid_telefoon', $lid_id ) );
    return generate_member_card( $bestuurder, $card_properties );
}

function bestuursfuncties_container_func( $atts ) {
    $args = array(
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
    $bestuursfuncties_array = array();
    $leden = new WP_Query( $args );
    if ( $leden->have_posts() ) {
        while ( $leden->have_posts() ) {
            $leden->the_post();
            $mail = get_field( 'clubfunctie_mail' );
            foreach ( get_field( 'clubfunctie_lid' ) as $bestuurder ) {
                $gender = get_field ( 'lid_gender', $bestuurder );
                $functie = get_correct_functie( get_the_title(), $gender );

                $bestuursfunctie = array(
                    'bestuurder' => $bestuurder,
                    'functie'    => $functie,
                    'mail'       => $mail
                );
                array_push( $bestuursfuncties_array, $bestuursfunctie );
            }
        }
    }
    wp_reset_postdata();

    // Herorder bestuursfuncties (zodat voorzitter/ondervoorzitter onder elkaar staat)
    $bestuursfuncties_reordered = array();
    $j = (int) floor( count( $bestuursfuncties_array ) / 2 );
    for ( $i = 0; $i < ceil( count( $bestuursfuncties_array ) / 2 ); $i++ ) {
        $bestuursfuncties_reordered[$i*2] = $bestuursfuncties_array[$i];
        if ( array_key_exists( $j+$i+1 , $bestuursfuncties_array)) {
            $bestuursfuncties_reordered[$i*2+1] = $bestuursfuncties_array[$j+$i+1];
        }
    }

    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( 'square' );
    $card_properties->set_card_relative_width( 'col-lg-6' );

    $bestuursfuncties = '';
    foreach ( $bestuursfuncties_reordered as $bestuurder ) {
        $bestuursfuncties .= _create_bestuurder_card( 
            $bestuurder['bestuurder'], 
            $card_properties,
            $bestuurder['functie'], 
            $bestuurder['mail'] );
    }
    
    return 
        "<div class='bestuurders-container row'>
            $bestuursfuncties
        </div>";
}
add_shortcode( 'bestuursfuncties_container', 'bestuursfuncties_container_func' );


function get_correct_functie( $functie, $gender ) {
    if ($functie == 'Ondervoorzitter' ) {
        return $gender == 'm' ? $functie : 'Ondervoorzitster';
    }
    return $functie;
}
?>