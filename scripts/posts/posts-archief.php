<?php

/**
 * 
 */
function posts_zoek_container_func( $atts ) {
    $type = get_field( 'pagina_archief_type' );
    switch( $type ) {
        case 'wedstrijdverslag':
            $postsachief_link = _get_wedstrijdverslagenarchief_link();
            $post_type = 'wedstrijdverslag';
            break;
        case 'nieuwsbericht':
            $postsachief_link = _get_nieuwsberichtenarchief_link();
            $post_type = 'post';
            break;
        default:
            $postsachief_link = '';
            $post_type = '';
            break;
    }

    return _posts_zoek_container( $postsachief_link, $post_type );
}
add_shortcode( 'posts_zoek_container', 'posts_zoek_container_func' );

function _posts_zoek_container( string $zoek_link, string $post_type ) : string {
    $huidig_jaar = date( 'Y' );

    $start_jaar_args = array(
        'post_type'      => $post_type,
        'orderby'        => 'date',
        'order'          => 'ASC',
        'posts_per_page' => 1
    );
    $start_jaar_query = new WP_Query( $start_jaar_args );
    $start_jaar = $huidig_jaar;
    if( $start_jaar_query->have_posts() ) {
        $start_jaar_query->the_post();
        $start_jaar = get_the_date( 'Y' );
    }

    $posts_link = get_home_url( NULL, $zoek_link );

    $jaar_knoppen = '';
    for( $i = $huidig_jaar; $i >= $start_jaar; $i-- ) {
        $posts_link_jaar = $posts_link . "?j=$i";
        $jaar_knoppen .= "<li><a class='dropdown-item' href='$posts_link_jaar'>$i</a></li>";
    }

    $jaar_knoppen_container = 
        "<div class='dropdown'>
            <a class='btn btn-secondary dropdown-toggle' href='#' role='button' id='jaarFilterDropDown' data-bs-toggle='dropdown' aria-expanded='false'>
                Filter op jaar
            </a>
  
            <ul class='dropdown-menu' aria-labelledby='jaarFilterDropDown'>
                $jaar_knoppen
            </ul>
        </div>";
        
    
    $zoek_filter_value = !empty( get_query_var( 'z' ) ) ? get_query_var( 'z' ) : '';
    $zoek_filter_container = 
        "<form role='search' method='get' class='form-inline' action='$posts_link'>
            <div class='input-group'>
                <input type='search' placeholder='Zoek...' id='search-form-input' class='form-control' value='$zoek_filter_value' name='z'>
            </div>
        </form>";

    if ( !empty( get_query_var( 'z' ) ) ) {
        $filter_key = '<i class="fas fa-search"></i>';
        $filter_value = get_query_var( 'z' );
    } else {
        $filter_key = '<i class="fas fa-calendar-alt"></i>';
        $filter_value = get_query_var( 'j' );
        if( empty( $filter_value ) ) {
            $filter_value = date( 'Y' );
        }
    }

    return 
        "<div class='archief-filter d-flex justify-content-between rounded p-2'>
            <div class='align-self-center d-none d-md-block'>
                <div class='d-flex align-items-center ms-3 overflow-hidden'>
                    <span class='filter-key fw-bolder me-3'>$filter_key</span>
                    <span class='filter-value fw-lighter text-truncate'>$filter_value<span>
                </div>
            </div>
            <div class='d-flex me-3'>
                <div class='ms-3'>$jaar_knoppen_container</div>
                <div class='ms-3'>$zoek_filter_container</div>
            </div>
        </div>";
}

/**
 * 
 */
function posts_archief_container_func( $atts ) {
    $post_type = get_field( 'pagina_archief_type' );
    $post_type = 'post';

    $posts_query = new WP_Query( _get_posts_args( $post_type ) );
    $posts = array();
    if( $posts_query->have_posts() ) {
        while( $posts_query->have_posts() ) {
            $posts_query->the_post();
            array_push( $posts, _create_type_object( $post_type ) );
        }
    }
    wp_reset_postdata();

    if( empty( $posts ) ) {
        $empty_label = _get_empty_label( $post_type );
        return 
            "<div class='archief-container posts-container d-flex justify-content-center my-4'>
                $empty_label
            </div>";
    }

    $posts_archief_container = '';
    foreach( $posts as $p => $post ) {
        $posts_archief_container .= _create_minimal_card( $post );
    }    

    return 
        "<div class='archief-container posts-container row gy-4'>
            $posts_archief_container
        </div>";
}
add_shortcode( 'posts_archief_container', 'posts_archief_container_func' );

function _get_posts_args( string $post_type ) : array {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $type = _get_wedstrijdverslag_type();
            break;
        case 'nieuwsbericht':
            $type = _get_nieuwsbericht_type();
            break;
        default:
            $type = _get_nieuwsbericht_type();
            break;
    }

    $jaar_filter = get_query_var( 'j' );
    $zoek_filter = get_query_var( 'z' );

    if( !empty( $jaar_filter ) ) {
        return _get_jaar_filter( $type, $jaar_filter );
    } elseif( !empty( $zoek_filter ) ) {
        return _get_zoek_filter( $type, $zoek_filter );
    } else {
        // Default: zoeken op huidig jaar
        $jaar_filter = date( 'Y' );
        return _get_jaar_filter( $type, $jaar_filter );
    }
}

function _get_zoek_filter( $post_type, $zoek_filter ) : array {
    return array(
        'post_type'      => $post_type,
        'posts_per_page' => 100,
        'post_status'    => 'publish',
        's'              => $zoek_filter
    );
}

function _get_jaar_filter( $post_type, $jaar_filter ) : array {
    return array(
        'post_type'      => $post_type,
        'date_query'     => array( array( 'year' => $jaar_filter ) ),
        'posts_per_page' => -1
    );
}

function _create_type_object( string $post_type ) {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $object = _create_wedstrijdverslag();
            break;
        case 'nieuwsbericht':
            $object = _create_nieuwsbericht();
            break;
        default:
            $object = _create_nieuwsbericht();
            break;
    }

    return $object;
}

function _get_empty_label( string $post_type ) : string {
    switch( $post_type ) {
        case 'wedstrijdverslag':
            $empty_label = _get_no_wedstrijdverslag_label();
            break;
        case 'nieuwsbericht':
            $empty_label = _get_no_nieuwsbericht_label();
            break;
        default:
            $empty_label = _get_no_nieuwsbericht_label();
            break;
    }

    return $empty_label;
}

function _create_minimal_card( $post_object ) : string {
    if ( $post_object->uitgelichte_afbeelding_id != NULL) {
        $foto_wrapper = wp_get_attachment_image( $post_object->uitgelichte_afbeelding_id, 'large', false, array( 'class' => 'object-fit-cover rounded' ) );
    } else {
        $foto_wrapper = 
            "<img src='https://asrieme.be/wp-content/uploads/2020/09/asrieme-logo-full-color-rgb.png' class='object-fit-scale p-3'>";
    }

    $meta_data_info = $post_object->get_meta_data_info();

    return 
        "<div class='post-card col-lg-4 col-md-6 col-sm-12'>
            <div class='post-card-inner'>
                <a href='$post_object->link'>
                    <div class='foto-wrapper d-flex align-items-center justify-content-center rounded'>
                        <div class='ratio ratio-16x9'>
                            $foto_wrapper
                        </div>
                        <div class='meer-lezen rounded px-1 ms-2 d-flex'>
                            <div>Meer lezen</div>
                            <div class='icon mx-2'><i class='fas fa-chevron-right'></i></div>
                        </div>
                    </div>
                    <div class='info-wrapper'>
                        $meta_data_info
                        <h4 class='titel'><strong>$post_object->titel</strong></h4>
                    </div>
                </a>
            </div>
        </div>";
}

?>