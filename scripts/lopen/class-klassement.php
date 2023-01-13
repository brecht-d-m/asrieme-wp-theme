<?php
namespace Klassement;

class Klassement {

    private string $titel = '';
    private string $slogan = '';
    private string $link = '';

    public function set_titel( $titel ) : void {
        $this->titel = $titel;
    }

    public function set_slogan( $slogan ) : void {
        $this->slogan = $slogan;
    }

    public function set_link( $link ) : void {
        $this->link = $link;
    }

    public function create_klassement_card() : string {
        $klassement_info = 
            "<div class='klassement-info'>
                <h4><strong>$this->titel</strong></h4>
                <p>$this->slogan</p>
            </div>";

        $info_knop = 
            "<div class='button-wrapper'>
                <a href='$this->link' title='Meer info'>
                    <div class='value-wrapper'>
                        <div class='text'>Meer info</div>
                        <div class='icon'><i class='fas fa-chevron-right'></i></div>
                    </div>
                </a>
            </div>";

        return 
            "<div class='klassement-card'>
                $klassement_info
                $info_knop
            </div>";  
    }

}

?>