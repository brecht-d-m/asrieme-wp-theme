<?php
namespace Werkgroep;

class Werkgroep {

    private string $titel = '';
    private string $naam = '';
    private string $info = '';
    private string $mail = '';
    private array $subwerkgroepen;
    private array $leads;

    public function set_titel( string $titel ) : void {
        $this->titel = $titel;
    }

    public function get_titel() : string {
        return $this->titel;
    }

    public function set_naam( string $naam ) : void {
        $this->naam = $naam;
    }

    public function get_naam() : string {
        return $this->naam;
    }

    public function set_info( string $info ) : void {
        $this->info = $info;
    }

    public function set_mail( string $mail ) : void {
        $this->mail = $mail;
    }

    public function set_subwerkgroepen( array $subwerkgroepen ) : void {
        $this->subwerkgroepen = $subwerkgroepen;
    }

    public function set_leads( array $leads ) : void {
        $this->leads = $leads;
    }

    public function create_werkgroep_card() : string {
        $groep_info     = $this->_create_info_part();
        $groep_contact  = $this->_create_mail_part();
        $groep_lead     = $this->_create_lead_part();
        $subgroepen     = $this->_create_sub_cards();
    
        return 
            "<div id='werkgroep-$this->naam' class='werkgroep-card'>
                <div class='werkgroep-card-inner rounded'>
                    <h3>$this->titel</h3>
                    <div class='info-wrapper'>
                        $groep_info
                        <div class='werkgroep-info'>
                            $groep_lead
                            $groep_contact
                            $subgroepen
                        </div>
                    </div>
                </div>
            </div>";   
    }
    
    private function _create_info_part() : string {
        return $this->_create_value_wrapper( $this->info, 'info', 'fa-info-circle' );
    }
    
    private function _create_mail_part() : string {
        return $this->_create_mail_wrapper( $this->mail );
    }

    private function _create_mail_wrapper( string $mail ) : string {
        if ( empty( $mail ) ) {
            return '';
        }
    
        $exploded_mails = explode( "\n", $mail );
        $adressen = '';
        foreach ( $exploded_mails as $adres ) {
            $adressen .= "<div class='mail-adres'><a href='mailto:$adres'>$adres</a></div>";
        }
    
        return $this->_create_value_wrapper( $adressen, 'mail', 'fa-envelope' );
    }
    
    private function _create_lead_part() : string {
        return $this->_create_lead_wrapper( $this->leads );
    }

    private function _create_lead_wrapper( array $leads ) : string {
        $nm_leads = count( $leads );
    
        $lead_tekst = 'Wordt geleid door ';
        $counter = 1;
        foreach ( $leads as $lead ) {
            $lead_tekst .= get_the_title( $lead->ID );
            if ( $counter != $nm_leads ) {
                $lead_tekst .= " & ";
            }
            $counter++;
        }
    
        if ( $counter == 1 ) {
            return '';
        }
        
        return $this->_create_value_wrapper( $lead_tekst, 'leads', 'fa-user' );
    }
    
    private function _create_sub_cards() : string {
        $subwerkgroepen_container = '';
        if ( empty( $this->subwerkgroepen ) ) {
            return '';
        }
    
        foreach ( $this->subwerkgroepen as $subwerkgroep ) {
            $sub_id = $subwerkgroep->ID;
    
            $leads = array();
            foreach ( get_field( 'werkgroep_lead', $sub_id ) as $lead_id ) {
                array_push( $leads, get_post( $lead_id ) );
            }
    
            $subwerkgroep_info = new Werkgroep();
            $subwerkgroep_info->set_titel( get_the_title( $sub_id ) );
            $subwerkgroep_info->set_mail( get_field( 'werkgroep_mail', $sub_id ) );
            $subwerkgroep_info->set_leads( $leads );
            $subwerkgroepen_container .= $this->_create_sub_card( $subwerkgroep_info );
        }
    
        return $subwerkgroepen_container;
    }
    
    private function _create_sub_card( Werkgroep $groep_info ) : string {
        $sub_mail_container = $this->_create_mail_wrapper( $groep_info->mail );
        $sub_lead_container = $this->_create_lead_wrapper( $groep_info->leads );
    
        return 
            "<div class='sub-werkgroep-card col-12'>
                <h4>$groep_info->titel</h4>
                <div class='sub-werkgroep-info'>
                    $sub_lead_container
                    $sub_mail_container
                </div>
            </div>";
    }

    private function _create_value_wrapper( string $value, string $type, string $fa_icon ) : string {
        if ( empty( $value ) ) {
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