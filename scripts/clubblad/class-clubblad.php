<?php
namespace Clubblad;

class Clubblad {

    public string $titel;
    public string $thema;
    public string $excerpt;
    public int $uitgelichte_afbeelding_id;
    public int $uitgave;
    public string $uitgave_datum;
    public string $inhoudstafel = '';
    public string $link = '';

    public function set_titel( string $titel = '' ) : void {
        $this->titel = $titel;
    }

    public function set_thema( string $thema = '' ) : void {
        $this->thema = $thema;
    }

    public function set_uitgelichte_afbeelding( int $uitgelichte_afbeelding_id ) : void {
        $this->uitgelichte_afbeelding_id = $uitgelichte_afbeelding_id;
    }

    public function set_uitgave( int $uitgave ) : void {
        $this->uitgave = $uitgave;
    }

    public function set_uitgave_datum( string $uitgave_datum ) : void {
        $this->uitgave_datum = $uitgave_datum;
    }

    public function set_inhoudstafel( string $inhoudstafel = '' ) : void {
        $this->inhoudstafel = $inhoudstafel;
    }

    public function set_link( string $link ) : void {
        $this->link = $link;
    }

    public function set_excerpt( string $excerpt = '' ) : void {
        $this->excerpt = $excerpt;
    }

    public function create_clubblad_card( Clubblad_Card_Properties $properties ) : string {
        if( $properties->card_minimaal ) {
            return $this->_create_minimaal_clubblad_card( $properties );
        } else {
            return $this->_create_standaard_clubblad_card( $properties );
        }
    }

    private function _create_minimaal_clubblad_card( Clubblad_Card_Properties $properties ) : string {
        $foto_wrapper = wp_get_attachment_image( $this->uitgelichte_afbeelding_id, 'medium' );

        $thema = empty( $this->thema ) ? '' : "<h4 class='thema'>$this->thema</h4>";
        $info_wapper = $this->_create_uitgave_container();

        $width_css_class = $properties->card_relative_width;
        return 
            "<div class='clubblad-card $width_css_class minimaal'>
                <div class='clubblad-card-inner rounded'>
                    <a href='$this->link'>
                        <div class='foto-wrapper'>
                            $foto_wrapper
                        </div>
                        <div class='info-wrapper'>
                            $thema
                            $info_wapper
                        </div>
                    </a>
                </div>
            </div>";
    }

    private function _create_standaard_clubblad_card( Clubblad_Card_Properties $properties ) : string {
        $foto_wrapper = wp_get_attachment_image( $this->uitgelichte_afbeelding_id, 'full' );

        $thema = empty( $this->thema ) ? '' : "<h4 class='thema'>$this->thema</h4>";
        $info_wrapper = $this->_create_uitgave_container();

        $inhoudstafel = $this->inhoudstafel;
        $inhoudstafel = str_replace( "\r", '', $inhoudstafel );
        $exploded_inhoudstafel = explode( "\n", $inhoudstafel );
        $inhoudstafel_listing = '';
        foreach( $exploded_inhoudstafel as $subtitel ) {
            $inhoudstafel_listing .= "<li>$subtitel</li>";
        }
        $inhoudstafel_listing = "<ul>$inhoudstafel_listing</ul>";

        $leer_meer_knop =
            "<div class='actie-knop'>
                <a href='$this->link' target='_blank'>Bekijk online</a>
            </div>";

        return 
            "<div class='clubblad-card $width_css_class standaard'>
                <div class='clubblad-card-inner row'>
                    <div class='foto-wrapper col-md-6'>
                        $foto_wrapper
                    </div>
                    <div class='info-wrapper col-md-6'>
                        $thema
                        $info_wrapper
                        <p>$this->excerpt</p>
                        $inhoudstafel_listing
                        $leer_meer_knop
                    </div>
                </div>
            </div>";
    }

    private function _create_uitgave_container() : string {
        $datum = \DateTime::createFromFormat( 'Ymd', $this->uitgave_datum )->getTimestamp();
        $format = new \IntlDateFormatter( 'nl_BE', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, NULL, \IntlDateFormatter::GREGORIAN, 'MMMM yyyy' );
        $formatted_uitgave_datum = ucfirst( $format->format( $datum ) );

        $uitgave = 
            "<div><span class='datum'>$formatted_uitgave_datum</span></div>
            <div><span class='uitgave'>Uitgave: </span>$this->uitgave</div>";
        return
            "<div class='info-icon-wrapper'>
                <div class='icon-wrapper sm'><i class='fas fa-calendar-alt'></i></div>
                <div class='value uitgave-wrapper'>$uitgave</div>
            </div>";
    }

}

?>