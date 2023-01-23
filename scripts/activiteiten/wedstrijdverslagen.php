<?php

require_once 'class-wedstrijdverslag.php';

use Wedstrijdverslag\Wedstrijdverslag;

function wedstrijdverslag_subtitel_func() {
    $author = get_the_author();
    $datum = get_the_date( 'l j F Y' );
    
    $terugknop = 
            "<div class='back-button-container'>
                <a href='https://www.asrieme.be/wedstrijdinfo/verslagenarchief'>
                    <div class='icon'><i class='fas fa-chevron-left'></i></div>
                    <div class='titel animated'>Verslagen</div>
                </a>
            </div>";

    return 
        "<div class='d-flex align-items-center'>
            $terugknop
            <div class='pagina-subtitel'>door $author &ensp; &vert; &ensp; $datum</div>
        </div>";
}
add_shortcode( 'wedstrijdverslag_subtitel', 'wedstrijdverslag_subtitel_func' );

function wedstrijdverslagen_titel_func() : string {
    $jaar_filter = get_query_var( 'jaar' );
    if( $jaar_filter == NULL ) {
        $jaar_filter = date( 'Y' );
    }

    return "<h2><strong>Wedstrijdverslagen $jaar_filter</strong></h2>";
} 
add_shortcode( 'wedstrijdverslagen_titel', 'wedstrijdverslagen_titel_func' );

function wedstrijdverslag_basis_link_func() : string {
    $huidig_jaar = date( 'Y' );
    return get_home_url( NULL, 'wedstrijdverslagen/?jaar=$i' );
}
add_shortcode( 'westrijdverslagen_basis_link', 'wedstrijdverslag_basis_link_func' );

function westrijdverslagen_zoek_container_func() : string {
    $huidig_jaar = date( 'Y' );
    $start_jaar = 2015;

    $jaar_knoppen = '';
    for( $i = $huidig_jaar; $i >= $start_jaar; $i-- ) {
        $verslagen_link = get_home_url( NULL, "wedstrijdoverzicht/verslagenarchief/?jaar=$i" );
        $jaar_knoppen .= "<li><a class='dropdown-item' href='$verslagen_link'>$i</a></li>";
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

    return 
        "<div class='archief-filter d-flex rounded px-2 py-3'>
            <div class='ms-3'>$jaar_knoppen_container</div>
        </div>";
}
add_shortcode( 'westrijdverslagen_zoek_container', 'westrijdverslagen_zoek_container_func' );

function westrijdverslagen_archief_container_func() : string {
    $verslagen_query = new WP_Query( _get_wedstrijdverslag_args() );
    $verslagen = array();
    if( $verslagen_query->have_posts() ) {
        while( $verslagen_query->have_posts() ) {
            $verslagen_query->the_post();
            array_push( $verslagen, _create_wedstrijdverslag() );
        }
    }
    wp_reset_postdata();

    $verslagen_container = '';
    foreach( $verslagen as $v => $verslag ) {
        $verslagen_container .= $verslag->create_wedstrijdverslag_card();
    }

    return 
        "<div class='wedstrijdverslagen-container row gy-4'>
            $verslagen_container
        </div>";
}
add_shortcode( 'westrijdverslagen_archief', 'westrijdverslagen_archief_container_func' );

function _get_wedstrijdverslag_args() : array {
    $jaar_filter = get_query_var( 'jaar' );
    if( $jaar_filter == NULL ) {
        $jaar_filter = date( 'Y' );
    }

    return array(
        'post_type'      => 'wedstrijdverslag',
        'date_query'     => array( array( 'year' => $jaar_filter ) ),
        'posts_per_page' => -1
    );
}

function _create_wedstrijdverslag() : Wedstrijdverslag {
    $wedstrijdverslag = new Wedstrijdverslag();
    $wedstrijdverslag->set_titel( get_the_title() );
    // eerst activiteit datum, dan get_the_date
    $wedstrijdverslag->set_datum( get_the_date( 'j F' ) );
    $wedstrijdverslag->set_link( get_the_permalink() );
    $wedstrijdverslag->set_gerelateerde_wedstrijd_id( get_field( 'wedstrijdverslag_wedstrijd' ) );
    $wedstrijdverslag->set_uitgelichte_afbeelding_id( get_post_thumbnail_id() );
    return $wedstrijdverslag;
}

?>