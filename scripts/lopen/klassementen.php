<?php

require_once 'class-klassement.php';

use Klassement\Klassement;

function klassementen_container_func( $atts ) {
    $a = shortcode_atts( array(
		'type' => ''
	), $atts );

    $type = $a['type'];
    if( $type == 'algemeen' 
            || $type == 'vergoeding' 
            || $type == 'korting' ) {
        $klassement_type = 'klassement_'.$type;
    } else {
        return '';
    }

    $klassementen = array();
    $query = new WP_Query( _get_klassement_args( $klassement_type ) );
    if( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            array_push( $klassementen, _create_klassement() );
        }
    }
    wp_reset_postdata();

    if( empty( $klassementen ) ) {
        return '';
    }

    $klassement_container = '';
    foreach ($klassementen as $k => $klassement ) {
        $klassement_container .= $klassement->create_klassement_card();
    }

    return 
        "<div class='klassementen-container'>
            $klassement_container
        </div>";
}
add_shortcode( 'klassementen_container', 'klassementen_container_func' );

function _get_klassement_args( $klassement_type ) : array {
    $klassement_args = array(
        'post_type'      => 'klassement',
        'posts_per_page' => -1,
        'orderby'        => 'klassement_seqNo',
    	'order'          => 'ASC'
    );

    if( !empty( $klassement_type ) ) {
        $tax_query = array(
            array(
                'taxonomy'  => 'type_klassement',
                'terms'     => $klassement_type,
                'field'     => 'slug',
                'operator'  => 'IN'
            )
        );
        $klassement_args['tax_query'] = $tax_query;
    }

    return $klassement_args;
}

function _create_klassement() : Klassement {
    $klassement = new Klassement();
    $klassement->set_titel( get_field( 'pagina_hoofdTitel' ) );
    $klassement->set_slogan( get_field( 'pagina_slogan' ) );
    $klassement->set_link( get_the_permalink() );
    return $klassement;
}

?>