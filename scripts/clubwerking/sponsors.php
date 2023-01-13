<?php

function sponsor_willekeurig_func ( $atts ) {
    $a = shortcode_atts( array(
		'bordered' => false
	), $atts );
    $bordered = $a['bordered'];

    $sponsor_args = array(
        'post_type'      => 'sponsor',
        'posts_per_page' => 1,
        'orderby'        => 'rand'
    );
    $query = new WP_Query( $sponsor_args );
    if ($query->have_posts()) {
        $query->the_post();
        $sponsor = create_sponsor_array( 'random' );
    }
    wp_reset_postdata();

    if ( $sponsor == NULL ) {
        return '';
    }

    //return create_sponsor_card( $sponsor, false, $bordered );
    return '';
}
add_shortcode( 'sponsor_willekeurig', 'sponsor_willekeurig_func' );

function sponsors_container_func ( $atts ) {
    $a = shortcode_atts( array(
        'bordered' => false,
		'type' => ''
	), $atts );
    $bordered = $a['bordered'];
    $type = $a['type'];
    if ( !empty ( $type )) {
        $sponsor_type = 'sponsor_'.$type;
    } else {
        $sponsor_type = '';
    }

    $sponsor_args = array(
        'post_type'      => 'sponsor',
        'posts_per_page' => -1,
        'orderby'        => 'rand',
        'order'          => 'ASC'
    );
    $sponsors = array();
    $query = new WP_Query( $sponsor_args );
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            if ( get_field( 'sponsor_type' )->slug == $sponsor_type || empty( $sponsor_type ) ) {
                $sponsor = create_sponsor_array( $type );
                array_push( $sponsors, $sponsor );
            }
        }
    }
    wp_reset_postdata();

    if ( empty( $sponsors ) ) {
        return '';
    }

    $sponsor_container = '';
    foreach ($sponsors as $s => $sponsor ) {
        $sponsor_container .= create_sponsor_card( $sponsor, true, $bordered );
    }

    return "<div class='sponsor-container'>$sponsor_container</div>";
}
add_shortcode( 'sponsors_container', 'sponsors_container_func' );

function get_sponsors_args( $sponsor_type ) {
    $sponsor_args = array(
        'post_type'      => 'sponsor',
        'posts_per_page' => -1,
        'orderby'        => 'rand',
        'order'          => 'ASC'
    );

    if ( !empty( $sponsor_type )) {
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

function create_sponsor_array( $type ) {
    return array(
        'naam'   => get_the_title(),
        'logo'   => get_field('sponsor_fotoLink'),
        'link'   => get_field('sponsor_link'),
        'slogan' => get_field('sponsor_slogan'),
        'type'   => $type
    );
}

function create_sponsor_card( $sponsor, $gebruik_info, $bordered ) {
    $naam = $sponsor['naam'];
    $logo = $sponsor['logo'];
    if ( !empty( $logo ) ) {
        $header = "<div class='logo'><img src='$logo'></div>";
    } else {
        $header = "<h3 class='titel'>$naam</h3>";
    }

    $slogan = $sponsor['slogan'];
    if ( !empty( $slogan ) && $gebruik_info ) {
        $slogan = 
            "<div class='slogan value-wrapper'>
                <div><i class='fas fa-info-circle'></i></div>
                <div class='value'><p>$slogan</p></div>
            </div>";
        
    } else {
        $slogan = '';
    }

    $link = $sponsor['link'];
    if ( !empty( $link ) && $gebruik_info ) {
        $link =
            "<div class='link value-wrapper'>
                <div><i class='fas fa-link'></i></div>
                <div class='value'><a href='$link' target='_blank'>$naam</a></div>
            </div>";
    } else {
        $link = '';
    }

    $sponsor_container = '';
    if ( !$gebruik_info ) {
        $sponsor_container = 
            "<a href='$link' class='sponsor-container-link' title='$naam'>
                $header
            </a>";
    } else {
        $sponsor_container = $header.$slogan.$link;
    }

    $type = $sponsor['type'];
    $bordered = $bordered ? 'bordered' : '';
    return 
        "<div class='sponsor-card $bordered $type'>
            $sponsor_container
        </div>";
}

?>