<?php

namespace Nieuwsbericht;

class Nieuwsbericht {

    public int $id;
    public string $titel;
    public string $datum;
    public string $link;
    public int $uitgelichte_afbeelding_id;

    public function set_id( int $id ) : void {
        $this->id = $id;
    }

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
        $tags = get_the_category( $this->id );
        $tag_list = '';
        if( !empty( $tags) ) {
            foreach( $tags as $t => $tag) {
                $tag_list .= $tag->name . ', ';
            }
            $tag_list = trim( $tag_list, ', ' );
        }
        return 
            "<div class='d-flex justify-content-between'>
                <div class='titel'>$tag_list</div> 
                <div class='datum'>$this->datum</div>
            </div>";
    }

}

?>