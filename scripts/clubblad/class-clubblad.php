<?php
namespace Clubblad;

class Clubblad {

    public string $titel;
    public string $thema;
    public int $uitgelichte_afbeelding_id;
    public int $uitgave;
    public string $uitgave_datum;
    public string $inhoudstafel = '';

    public function set_titel( string $titel ) : void {
        $this->titel = $titel;
    }

    public function set_thema( string $thema ) : void {
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

    public function set_inhoudstafel( string $inhoudstafel ) : void {
        $this->inhoudstafel = $inhoudstafel;
    }

    public function create_clubblad_card( Clubblad_Card_Properties $properties ) : string {
        if ( $properties->card_minimaal ) {
            return $this->_create_minimaal_clubblad_card( $properties );
        } else {
            return $this->_create_standaard_clubblad_card( $properties );
        }
    }

    private function _create_minimaal_clubblad_card( Clubblad_Card_Properties $properties ) : string {
        $foto_url = the_attachment_link( $this->uitgelichte_afbeelding_id );
        $foto_wrapper = "<img src='$foto_url'>";

        $thema = empty( $this->thema ) ? '' : "<h4 class='thema'>$this->thema</h4>";

        $datum = DateTime::createFromFormat( 'Ymd', $this->uitgave_datum )->getTimestamp();
        $format = new IntlDateFormatter( 'nl_BE', IntlDateFormatter::FULL, IntlDateFormatter::FULL, NULL, IntlDateFormatter::GREGORIAN, 'MMMM yyyy' );
        $formatted_uitgave_datum = $format->format( $datum );
        $uitgave = 
            "<div>
                <div><span class='datum'>$formatted_uitgave_datum</span></div>
                <div><span class='uitgave'>Uitgave: </span>$this->uitgave</div>
            </div>";
        $info_wapper = 
            "<div class='value-wrapper'>
                <div class='icon'><i class='fas fa-fa-calendar-alt'></i></div>
                <div class='value uitgave'>$uitgave</div>
            </div>";

        $width_css_class = $member_card_properties->card_relative_width;
        return 
            "<div class='clubblad-card $width_css_class minimaal'>
                <div class='clubblad-card rounded'>
                    <div class='foto-wrapper'>
                        $foto_wrapper
                    </div>
                    <div class='info-wrapper'>
                        $thema
                        $info_wapper
                    </div>
                </div>
            </div>";
    }

    private function _create_standaard_clubblad_card( Clubblad_Card_Properties $properties ) : string {

    }

}

?>