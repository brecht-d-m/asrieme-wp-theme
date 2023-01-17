<?php
function post_subtitel_func() {
    $author = get_the_author();
    $datum = get_the_date( 'l j F Y' );
    return "<div class='bericht subtitel'>door $author &ensp; &vert; &ensp; $datum</div>";
}
add_shortcode( 'post_subtitel', 'post_subtitel_func' );

function laatste_posts_container_func () {
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
        while ( $posts_results->have_posts() ) {
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
?>