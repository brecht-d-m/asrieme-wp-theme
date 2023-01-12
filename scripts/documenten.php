<?php

function documenten_container_func( $atts ) {
    $a = shortcode_atts( array(
		'veld'   => '',
        'target' => ''
	), $atts );

    $target = $a['target'];
    $veld_documenten = $a['veld'];
    if ( empty( $veld_documenten )) {
        return '';
    }

    $document_links = get_field( $veld_documenten );
    $exploded_links = explode( "\n", $document_links );

    $listed_documents = '';
    foreach ( $exploded_links as $attachment_link ) {
        $uploads = wp_get_upload_dir();
        $attachment = trim( str_replace( $uploads['baseurl'].'/', '', $attachment_link ) );
        $attachment_args = array(
            'post_type'        => 'attachment',
            'post_status'      => 'any',
            'posts_per_page'   => 1,
            'meta_query'       => array(
				array(
					'value'   => $attachment,
					'compare' => 'LIKE',
					'key'     => '_wp_attached_file',
				)
			)
        ); 

        $attachment_result = new WP_Query( $attachment_args );
        if ( $attachment_result->have_posts() ) {
            $attachment_result->the_post();
            $attachment_title = get_the_title();
    
            $listed_documents .= 
                "<li class='document'>
                    <a href='$attachment_link' target='$target'>
                        $attachment_title
                    </a>
                </li>";
        }   

        wp_reset_postdata();
    }

    return
            "<div class='document-container'>
                <ul>
                    $listed_documents
                </ul>
            </div>";


}
add_shortcode( 'documenten_container', 'documenten_container_func' );

?>