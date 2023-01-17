<?php

function socials_top_func() {
    $facebook_knop = _get_button_facebook();
    $instagram_knop = _get_button_instagram();
    $flickr_knop = _get_button_flickr( false );
    return 
        "<div class='social-buttons-container d-flex justify-content-end'>
            $facebook_knop
            $instagram_knop
            $flickr_knop
        </div>";
}
add_shortcode( 'socials_top', 'socials_top_func' );

function socials_bottom_func() {
    return _social_button_bar( false );
}
add_shortcode( 'socials_bottom', 'socials_bottom_func' );

function socials_contact_func() {
    return _social_button_bar( true, 'contact' );
}
add_shortcode( 'socials_contact', 'socials_contact_func' );

function _social_button_bar( bool $include_flickr, string $extra_css_class = '' ) : string {
    $facebook_knop = _get_button_facebook();
    $instagram_knop = _get_button_instagram();
    $strava_knop = _get_button_strava();
    $twizzit_knop = _get_button_twizzit();
    $flickr_knop = $include_flickr ? _get_button_flickr( true ) : '';
    return 
        "<div class='social-buttons-container d-flex justify-content-center $extra_css_class'>
            $facebook_knop
            $instagram_knop
            $strava_knop
            $flickr_knop
            $twizzit_knop
        </div>";
}

function _get_button_facebook() : string {
    $url = 'https://www.facebook.com/asrieme';
    $knop_titel = 'Volg ons op Facebook';
    $knop_class = 'icon-facebook';
    return _create_social_button( $url, 'facebook', $knop_class, $knop_titel );
}

function _get_button_instagram() : string {
    $url = 'https://www.instagram.com/as.rieme';
    $knop_titel = 'Volg ons op Instagram';
    $knop_class = 'icon-instagram';
    return _create_social_button( $url, 'instagram', $knop_class, $knop_titel );
}

function _get_button_strava() : string {
    $url = 'https://www.strava.com/clubs/asrieme';
    $knop_titel = 'Volg ons op Strava';
    $knop_class = 'icon-strava';
    return _create_social_button( $url, 'strava', $knop_class, $knop_titel );
}

function _get_button_twizzit() : string {
    $url = 'https://app.twizzit.com/v2/home?organization-id=29845';
    $knop_titel = 'Volg ons op Twizzit';
    $knop_class = 'icon-twizzit';
    return _create_social_button( $url, 'twizzit', $knop_class, $knop_titel );
}

function _get_button_flickr( bool $is_icon ) : string {
    $url = 'https://www.flickr.com/photos/asrieme/albums';
    $knop_titel = 'Volg ons op Flickr';
    if( $is_icon ) {
        $knop_class = 'fab fa-flickr';
        return _create_social_button( $url, 'flickr', $knop_class, $knop_titel );
    } else {
        return
            "<div class='social-button social-text-button rounded flickr'>
                <a href='$url' target='_blank' title='$knop_titel'>
                    <div class='titel-wrapper'>
                        <div class='icon'><i class='fas fa-camera'></i></div>
                        <div class='text'>fotos</div>
                    </div>
                </a>
            </div>";
    }
}

function _create_social_button( string $url, string $media, string $knop_class, string $knop_titel ) : string {
    return 
        "<div class='social-button social-icon-button rounded $media'>
            <a href='$url' target='_blank' title='$knop_titel'>
                <i class='$knop_class'></i>
            </a>
        </div>";
}

?>