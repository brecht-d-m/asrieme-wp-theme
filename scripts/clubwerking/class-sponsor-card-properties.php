<?php 
namespace Sponsor;

class Sponsor_Card_Properties {

    public string $relative_width = '';
    public bool $gebruik_extra_info = false;
    public bool $bordered = true;

    public function set_card_relative_width( string $relative_width ) : void {
        $this->relative_width = $relative_width;
    }

    public function set_gebruik_extra_info( bool $gebruik_extra_info ) : void {
        $this->gebruik_extra_info = $gebruik_extra_info;
    }

    public function set_bordered( bool $bordered ) : void {
        $this->bordered = $bordered;
    }

}

?>