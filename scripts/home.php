<?php

function copyright_footer_func() {
    $huidig_jaar = date('Y');
    return "<div class='copy-right'>&copy; $huidig_jaar AS Rieme VZW</div>";
}
add_shortcode( 'copyright_footer', 'copyright_footer_func' );

function contact_footer_func() {
    $home_id = _get_home_id();

    $adres = get_field( 'pagina_home_adres', $home_id );
    $adres = str_replace( "\r", '', $adres );
    $exploded_adres = explode( "\n", $adres );
    $adres_container = '';
    foreach ($exploded_adres as $line) {
        $adres_container .= "<p>$line</p>";
    }

    $btw = get_field( 'pagina_home_btw', $home_id );
    $iban = get_field( 'pagina_home_iban', $home_id );
    $bic = get_field( 'pagina_home_bic', $home_id );

    return
        "<div class='contact footer'>
            <div class='adres'>$adres_container</div>
            <div class='onderneming-info'>
                <div class='btw'>BTW: $btw</div>
                <div class='iban'>IBAN: $iban</div>
                <div class='bic'>BIC: $bic</div>
            </div>
        </div>";

}
add_shortcode( 'contact_footer', 'contact_footer_func' );

function adres_container_func() {
    $home_id = _get_home_id();

    $adres = get_field( 'pagina_home_adresPiste', $home_id );
    $adres = str_replace( "\r", '', $adres );
    $exploded_adres = explode( "\n", $adres );
    $adres_container = '';
    foreach ($exploded_adres as $line) {
        $adres_container .= "<div>$line</div>";
    }
    return 
        "<div class='adres-container piste'>
            $adres_container
        </div>";
}
add_shortcode( 'adres_container', 'adres_container_func' );

function _get_home_id() : int {
    $query = new WP_Query([
        'post_type' => 'page',
        'name' => 'home'
    ]);
    $home_id = NULL;
    if ( $query->have_posts() ) {
        $query->the_post();
        $home_id = get_the_ID();
    }
    wp_reset_postdata();
    return $home_id;
}

?>