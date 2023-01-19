<?php

function documenten_container_func( $atts ) {
    $a = shortcode_atts( array(
        'veld'   => '',
        'target' => ''
    ), $atts );

    $veld_documenten = $a['veld'];
    if( empty( $veld_documenten ) ) {
        return '';
    }

    $target = $a['target'];

    $document_links = get_field( $veld_documenten );
    $document_links = str_replace( "\r", '', $document_links );
    $exploded_links = explode( "\n", $document_links );

    $listed_documents = '';
    foreach( $exploded_links as $attachment_url ) {
        if( !empty( $attachment_url ) ) {
            $attachment_link = _get_attachment_link( $attachment_url, $target );
            $listed_documents .= 
                "<li class='document'>
                    $attachment_link
                </li>";
        }
    }

    return
        "<div class='document-container'>
            <ul>
                $listed_documents
            </ul>
        </div>";


}
add_shortcode( 'documenten_container', 'documenten_container_func' );

function document_link_func( $atts ) {
    $a = shortcode_atts( array(
        'veld'   => '',
        'target' => '',
        'titel'  => ''
    ), $atts );

    
    $veld_document = $a['veld'];
    if( empty( $veld_document ) ) {
        return '';
    }

    $target = $a['target'];
    $titel = $a['titel'];

    $document_url = get_field( $veld_document );
    $document_url = str_replace( "\r", '', $document_url );
    if( empty( $document_url ) ) {
        return '';
    } else {
        return _get_attachment_link( $document_url, $target, $titel );
    }  
}
add_shortcode( 'document_link', 'document_link_func' );

function _get_attachment_link( string $attachment_url, string $target = '', string $titel = '' ) : string {
    $uploads = wp_get_upload_dir();
    $attachment = trim( str_replace( $uploads['baseurl'] . '/', '', $attachment_url ) );
    $attachment_args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array(
                'value'   => $attachment,
                'compare' => 'LIKE',
                'key'     => '_wp_attached_file',
            )
        )
    ); 

    $attachment_link = '';
    $attachment_result = new WP_Query( $attachment_args );
    if( $attachment_result->have_posts() ) {
        $attachment_result->the_post();
        $attachment_title = empty( $titel ) ? get_the_title() : $titel ;

        $attachment_link =
            "<a href='$attachment_url' target='$target'>$attachment_title</a>";
    }   

    wp_reset_postdata();
    return $attachment_link;
}

?>