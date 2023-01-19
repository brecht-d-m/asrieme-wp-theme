<?php
namespace Clubblad;

class Clubblad_Card_Properties {

    public bool $card_minimaal = true;
    public string $card_relative_width = '';

    public function set_card_minimaal( bool $card_minimaal ) : void {
        $this->card_minimaal = $card_minimaal;
    }

    public function set_card_relative_width( string $card_relative_width ) : void {
        $this->card_relative_width = $card_relative_width;
    }

}

?>