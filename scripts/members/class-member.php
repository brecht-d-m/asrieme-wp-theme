<?php
namespace Member;

class Member {

    public string $naam = '';
    public string $functie = '';
    public string $functie_beschrijving = '';
    public string $mail = '';
    public string $telefoon = '';
    public string $foto = '';
    public string $member_type = '';

    public function set_naam( string $naam ) : void {
        $this->naam = $naam;
    }

    public function set_functie( string $functie ) : void {
        $this->functie = $functie;
    }

    public function set_functie_beschrijving( string $functie_beschrijving ) : void {
        $this->functie_beschrijving = $functie_beschrijving;
    }

    public function set_mail( string $mail ) : void {
        $this->mail = $mail;
    }

    public function set_telefoon( string $telefoon ) : void {
        $this->telefoon = $telefoon;
    }

    public function set_foto( string $foto ) : void {
        $this->foto = $foto;
    }

    public function set_member_type( string $member_type ) : void {
        $this->member_type = $member_type;
    }

    public function create_member_card( Member_Card_Properties $member_card_properties ) : string {
        // Left part - Member foto
        $foto_wrapper = $this->_create_foto_element( $member_card_properties );
    
        // Right part - Member info
        $functie = empty( $this->functie ) ? '' : "<div class='functie'>$this->functie</div>";
        $functie_beschrijving = empty( $this->functie_beschrijving ) ? '' : "<div class='functie-beschrijving'>$this->functie_beschrijving</div>";
    
        $mail = $this->mail;
        if( !empty( $mail ) ) {
            $mail = "<a href='mailto:$mail'>$mail</a>";
        }
        $mail = $this->_create_value_wrapper( $mail, 'mail', 'envelope' );
    
        $telefoon = $this->_create_value_wrapper( $this->telefoon, 'telefoon', 'phone' );
        $contact_container = '';
        if( !empty( $mail ) || !empty( $telefoon ) ) {
            $contact_container = 
                "<div class='contact-info-container'>
                    $mail
                    $telefoon
                </div>";
        }
    
        $width_css_class = $member_card_properties->card_relative_width;
        $foto_stretch = empty( $this->foto ) && $member_card_properties->foto_stretch ? 
            'stretch-foto' : '';
        return 
            "<div class='member-card $width_css_class'>
                <div class='member-card-inner rounded'>
                    <div class='foto-wrapper rounded $foto_stretch'>
                        $foto_wrapper
                    </div>
                    <div class='info-wrapper'>
                        <h4 class='naam'>$this->naam</h4>
                        $functie
                        $functie_beschrijving
                        $contact_container
                    </div>
                </div>
            </div>";
    }
    
    private function _create_foto_element( Member_Card_Properties $member_card_properties ) : string {
        $naam = $this->naam;
        $foto_aspect_ratio = $member_card_properties->foto_aspect_ratio;
        $foto_grootte = $member_card_properties->foto_vergroot == true ? 'bg' : 'sm';
    
        $foto = $this->foto;
        if( empty ($foto ) ) {
            $foto = 'https://asrieme.be/wp-content/uploads/2020/09/asrieme-logo-full-color-rgb.png';
            $foto_aspect_ratio = "ratio-unknown";
        }
        
        return
            "<img src='$foto' alt='$naam' class='rounded $foto_aspect_ratio $foto_grootte'>";
    }
    
    private function _create_value_wrapper( string $value, string $type, string $fa_icon ) : string {
        if( empty( $value ) ) {
            return '';
        }
        
        return 
            "<div class='value-wrapper'>
                <div class='icon'><i class='fas fa-$fa_icon'></i></div>
                <div class='value $type'>$value</div>
            </div>";
    }
}

?>