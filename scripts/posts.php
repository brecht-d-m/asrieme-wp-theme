<?php
function post_subtitel_func() {
    $datum = get_the_date( 'l j F' );
    $user_info = get_userdata( get_current_user_id() );
    $voornaam = $user_info->first_name; 

    return "<div class='bericht subtitel'>$voornaam &ensp; &vert; &ensp; $datum</div>";
}
add_shortcode( 'post_subtitel', 'post_subtitel_func' );

function laatste_posts_container_func() {
    return _laatste_posts( 'post', 'Laatste nieuws' );
}
add_shortcode( 'laatste_posts_container', 'laatste_posts_container_func' );

function _laatste_posts( $post_type, $label_titel ) {
    $posts_titels = '';
    $posts_args = array(
        'posts_per_page' => 5,
        'orderby'        => 'post_date',
        'order'          => 'DESC',
        'post_type'      => $post_type
    );
    $posts_results = new WP_Query( $posts_args );
    if( $posts_results->have_posts() ) {
        while( $posts_results->have_posts() ) {
            $posts_results->the_post();
            $post_datum = get_the_date( 'd/m' );
            $post_titel = get_the_title();
            $post_link = get_the_permalink();
    
            $posts_titels .= 
                "<div class='post'>
                    <a href='$post_link' titel='$post_titel'>
                        <span class='datum'>$post_datum</span> <span class='titel'>$post_titel</span>
                    </a>
                </div>";
        }

        $posts_titels =
            "<div class='laatste-posts-container rounded'>
                <h2><strong>$label_titel</strong></h2>
                <div class='posts'>
                    $posts_titels
                </div>
            </div>";
    }
    wp_reset_postdata();

    return $posts_titels;
}

function _post_subtitel( string $link, string $titel ) : string {
    $author = get_the_author();
    $datum = get_the_date( 'l j F Y' );

    $terug_link = get_home_url( NULL, $link );
    
    $terugknop = 
            "<div class='back-button-container'>
                <a href='$terug_link'>
                    <div class='icon'><i class='fas fa-chevron-left'></i></div>
                    <div class='titel animated'>$titel</div>
                </a>
            </div>";

    return 
        "<div class='d-flex align-items-center'>
            $terugknop
            <div class='pagina-subtitel'>door $author &ensp; &vert; &ensp; $datum</div>
        </div>";
}

function _posts_zoek_container( string $zoek_link ) : string {
    $huidig_jaar = date( 'Y' );
    $start_jaar = 2015;

    $verslagen_link = get_home_url( NULL, $zoek_link );

    $jaar_knoppen = '';
    for( $i = $huidig_jaar; $i >= $start_jaar; $i-- ) {
        $verslagen_link_jaar = $verslagen_link . "?j=$i";
        $jaar_knoppen .= "<li><a class='dropdown-item' href='$verslagen_link_jaar'>$i</a></li>";
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
        
    
    $zoek_filter_container = 
        "<form role='search' method='get' class='form-inline' action='$verslagen_link'>
            <div class='input-group'>
                <input type='search' placeholder='Zoek...' id='search-form-input' class='form-control' value='' name='z'>
            </div>
        </form>";

    if ( !empty( get_query_var( 'z' ) ) ) {
        $filter_key = '<i class="fas fa-search"></i>';
        $filter_value = get_query_var( 'z' );
    } else {
        $filter_key = '<i class="fas fa-calendar-alt"></i>';
        $filter_value = get_query_var( 'j' );
        if( empty( $filter_value ) ) {
            $filter_value =  date( 'Y' );
        }
    }

    return 
        "<div class='archief-filter d-flex justify-content-between rounded p-2'>
            <div class='d-flex align-items-center ms-3 overflow-hidden'>
                <span class='filter-key fw-bolder me-3'>$filter_key</span>
                <span class='filter-value fw-lighter text-truncate'>$filter_value<span>
            </div>
            <div class='d-flex me-3'>
                <div class='ms-3'>$jaar_knoppen_container</div>
                <div class='ms-3'>$zoek_filter_container</div>
            </div>
        </div>";
}

function _get_posts_args( string $post_type ) : array {
    $jaar_filter = get_query_var( 'j' );
    $zoek_filter = get_query_var( 'z' );

    if( !empty( $jaar_filter ) ) {
        return _get_jaar_filter( $post_type, $jaar_filter );
    } else if( !empty( $zoek_filter ) ) {
        return _get_zoek_filter( $post_type, $zoek_filter );
    } else {
        // Default: zoeken op huidig jaar
        $jaar_filter = date( 'Y' );
        return _get_jaar_filter( $post_type, $jaar_filter );
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

?>