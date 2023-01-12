<?php
namespace Member;

use Member\{
    Member,
    Member_Card_Properties,
};

function generate_member_card( Member $member, Member_Card_Properties $member_card_properties ) : string {
    // Left part - Member foto
    $foto_wrapper = _create_foto_element( $member, $member_card_properties );

    // Right part - Member info
    $functie = empty( $member->functie ) ? '' : "<div class='functie'>$member->functie</div>";
    $functie_beschrijving = empty( $member->functie_beschrijving ) ? '' : "<div class='functie-beschrijving'>$member->functie_beschrijving</div>";

    $mail = $member->mail;
    if ( !empty( $mail ) ) {
        $mail = "<a href='mailto:$mail'>$mail</a>";
    }
    $mail = _create_value_wrapper( $mail, 'mail', 'envelope' );

    $telefoon = _create_value_wrapper( $member->telefoon, 'telefoon', 'phone' );
    $contact_container = '';
    if ( !empty( $mail ) || !empty( $telefoon ) ) {
        $contact_container = 
            "<div class='contact-info-container'>
                $mail
                $telefoon
            </div>";
    }

    $width_css_class = $member_card_properties->card_relative_width;
    return 
        "<div class='member-card $width_css_class'>
            <div class='member-card-inner rounded'>
                <div class='foto-wrapper'>
                    $foto_wrapper
                </div>
                <div class='info-wrapper'>
                    <h4 class='naam'>$member->naam</h4>
                    $functie
                    $functie_beschrijving
                    $contact_container
                </div>
            </div>
        </div>";
}

function _create_foto_element( Member $member, Member_Card_Properties $member_card_properties ) : string {
    $naam = $member->naam;
    $foto_aspect_ratio = $member_card_properties->foto_aspect_ratio;
    $foto_grootte = $member_card_properties->foto_vergroot == true ? 'bg' : 'sm';

    $foto = $member->foto;
    if ( empty ($foto ) ) {
        $foto = 'https://asrieme.be/wp-content/uploads/2020/09/asrieme-logo-full-color-rgb.png';
        $foto_aspect_ratio = "ratio-unknown";
    }

    
    return
        "<img src='$foto' alt='$naam' class='rounded $foto_aspect_ratio $foto_grootte'>";
}

function _create_value_wrapper( string $value, string $type, string $fa_icon ) : string {
    if ( empty( $value ) ) {
        return '';
    }
    return 
        "<div class='value-wrapper'>
            <div class='icon'><i class='fas fa-$fa_icon'></i></div>
            <div class='value $type'>$value</div>
        </div>";
}

?>