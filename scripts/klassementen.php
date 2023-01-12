<?php

function klassementen_container_func( $atts ) {
    $a = shortcode_atts( array(
		'type' => ''
	), $atts );

    $type = $a['type'];
    if ( $type == 'algemeen' 
            || $type == 'vergoeding' 
            || $type == 'korting' ) {
        $klassement_type = 'klassement_'.$type;
    } else {
        return '';
    }

    $klassementen = array();
    $query = new WP_Query( get_klassement_args( $klassement_type ));
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $klassement = array(
                'titel'  => get_field('pagina_hoofdTitel'),
                'slogan' => get_field('pagina_slogan'),
                'link'   => get_the_permalink()
            );
            array_push( $klassementen, $klassement );
        }
    }
    wp_reset_postdata();

    if ( empty( $klassementen )) {
        return '';
    }

    return create_klassement_container( $klassementen );
}
add_shortcode( 'klassementen_container', 'klassementen_container_func' );

function get_klassement_args( $klassement_type ) {
    $klassement_args = array(
        'post_type'      => 'klassement',
        'posts_per_page' => -1,
        'orderby'        => 'klassement_seqNo',
    	'order'          => 'ASC'
    );

    if ( !empty( $klassement_type )) {
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

function create_klassement_container( $klassementen ) {
    $klassement_container = '';
    foreach ($klassementen as $klassement ) {
        $titel  = $klassement['titel'];
        $slogan = $klassement['slogan'];
        $link   = $klassement['link'];

        $klassement_info = 
            "<div class='klassement-info'>
                <h4><strong>$titel</strong></h4>
                <p>$slogan</p>
            </div>";

        $info_knop = 
            "<div class='button-wrapper'>
                <a href='$link' title='Meer info'>
                    <div class='icon-wrapper'>
                        <div class='text'>Meer info</div>
                        <div><i class='fas fa-chevron-right'></i></div>
                    </div>
                </a>
            </div>";

        $klassement_container .= 
            "<div class='klassement'>
                $klassement_info
                $info_knop
            </div>";  
    }

    return 
        "<div class='klassement-container'>
            $klassement_container
        </div>";
}

?>