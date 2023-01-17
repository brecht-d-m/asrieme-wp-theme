<?php
namespace Sponsor;

class Sponsor {

    private string $naam = '';
    private string $logo = '';
    private string $link = '';
    private string $slogan = '';
    private string $type = '';

    public function set_naam( string $naam ) : void {
        $this->naam = $naam;
    }

    public function set_logo( string $logo ) : void {
        $this->logo = $logo;
    }

    public function set_link( string $link ) : void {
        $this->link = $link;
    }

    public function set_slogan( string $slogan ) : void {
        $this->slogan = $slogan;
    }

    public function set_type( string $type ) : void {
        $this->type = $type;
    }

    public function create_sponsor_card( Sponsor_Card_Properties $properties ) : string {
        $header = "<div class='logo-wrapper'><img src='$this->logo'></div>";
        $slogan_wrapper = $this->_create_value_wrapper( $this->slogan, 'slogan', 'fa-info-circle', $properties->gebruik_extra_info );

        $sponsor_card = 
            "<a href='$this->link' class='sponsor-card-link' title='$this->naam'>
                $header
                <div class='info-wrapper'>
                    $slogan_wrapper
                </div>
            </a>";

        $bordered = $properties->bordered ? 'border' : '';
        return 
            "<div class='sponsor-card $properties->relative_width $this->type'>
                <div class='sponsor-card-inner rounded $bordered'>
                    $sponsor_card
                </div>
            </div>";
    }

    private function _create_value_wrapper( string $value, string $type, string $fa_icon, bool $gebruik_info ) : string {
        if( empty( $value ) || !$gebruik_info ) {
            return '';
        }

        return 
            "<div class='value-wrapper'>
                <div class='icon'><i class='fas $fa_icon'></i></div>
                <div class='value $type'>$value</div>
            </div>";
    }

}

?>