<?php

namespace Wedstrijdverslag;

class Wedstrijdverslag {

    public string $titel;
    public string $datum;
    public string $link;
    public int $gerelateerde_wedstrijd_id;
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

    public function set_gerelateerde_wedstrijd_id( int $gerelateerde_wedstrijd_id ) : void {
        $this->gerelateerde_wedstrijd_id = $gerelateerde_wedstrijd_id;
    }

    public function get_meta_data_info() : string {
        if ( $this->gerelateerde_wedstrijd_id != NULL ) {
            $wedstrijd_titel = get_field( 'activiteit_titel', $this->gerelateerde_wedstrijd_id );
        } else {
            $wedstrijd_titel = '';
        }

        return 
            "<div class='d-flex justify-content-between'>
                <div class='titel'>$wedstrijd_titel</div> 
                <div class='datum'>$this->datum</div>
            </div>";
    }

}

?>