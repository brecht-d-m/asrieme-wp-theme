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

    public function create_archief_card() : string {
        if ( $this->uitgelichte_afbeelding_id != NULL) {
            $foto_wrapper = wp_get_attachment_image( $this->uitgelichte_afbeelding_id, 'large', false, array( 'class' => 'rounded' ) );
        } else {
            $foto_wrapper = "<img src='https://asrieme.be/wp-content/uploads/2020/09/asrieme-logo-full-color-rgb.png' class='no-image p-3'>";
        }

        return 
            "<div class='post-card col-lg-4 col-md-6 col-sm-12'>
                <div class='post-card-inner'>
                    <a href='$this->link'>
                        <div class='foto-wrapper d-flex align-items-center justify-content-center rounded'>
                            $foto_wrapper
                            <div class='meer-lezen rounded px-1 ms-2 d-flex'>
                                <div>Meer lezen</div>
                                <div class='icon mx-2'><i class='fas fa-chevron-right'></i></div>
                            </div>
                        </div>
                        <div class='info-wrapper'>
                            <div class='d-flex justify-content-end'>
                                <div class='datum'>$this->datum</div>
                            </div>
                            <h4 class='titel'><strong>$this->titel</strong></h4>
                        </div>
                    </a>
                </div>
            </div>";
    }

    public function create_info_card() : string {
        return '';
    }

}

?>