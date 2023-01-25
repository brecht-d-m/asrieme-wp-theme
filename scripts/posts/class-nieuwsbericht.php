<?php

namespace Nieuwsbericht;

class Nieuwsbericht {

    public string $titel;
    public string $datum;
    public string $link;
    public int $uitgelichte_afbeelding_id;

    public function set_titel( string $titel ) : void {
        $this->titel = $titel;
    }

    public function set_datum( string $datum ) : void {
        $this->datum = $datum;
    }

    public function set_link( string $link ) : void {
        $this->link = $link;
    }

    public function set_uitgelichte_afbeelding_id( int $uitgelichte_afbeelding_id ) : void {
        $this->uitgelichte_afbeelding_id = $uitgelichte_afbeelding_id;
    }

    public function get_meta_data_info() : string {
        return 
            "<div class='d-flex justify-content-between'>
                <div class='datum'>$this->datum</div>
            </div>";
    }

}

?>