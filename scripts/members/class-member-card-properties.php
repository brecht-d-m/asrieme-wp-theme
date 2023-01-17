<?php 
namespace Member;

class Member_Card_Properties {
    public string $relative_width = '';
    public string $foto_aspect_ratio = '';
    public bool $foto_stretch = false;
    public bool $foto_vergroot = false;

    public function set_card_relative_width( string $card_relative_width ) : void {
        $this->card_relative_width = $card_relative_width;
    }

    public function set_foto_aspect_ratio( string $foto_aspect_ratio ) : void {
        $this->foto_aspect_ratio = $foto_aspect_ratio;
    }

    public function set_foto_stretch( bool $foto_stretch ) : void {
        $this->foto_stretch = $foto_stretch;
    }

    public function set_foto_vergroot( bool $foto_vergroot ) : void {
        $this->foto_vergroot = $foto_vergroot;
    }
}

?>