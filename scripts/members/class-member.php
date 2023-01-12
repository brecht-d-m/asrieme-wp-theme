<?php
namespace Member;

class Member {

    public string $naam = '';
    public string $functie = '';
    public string $functie_beschrijving = '';
    public string $mail = '';
    public string $telefoon = '';
    public string $foto = '';

    public function set_naam( string $naam ) : Member {
        $this->naam = $naam;
        return $this;
    }

    public function set_functie( string $functie ) : Member {
        $this->functie = $functie;
        return $this;
    }

    public function set_functie_beschrijving( string $functie_beschrijving ) : Member {
        $this->functie_beschrijving = $functie_beschrijving;
        return $this;
    }

    public function set_mail( string $mail ) : Member {
        $this->mail = $mail;
        return $this;
    }

    public function set_telefoon( string $telefoon ) : Member {
        $this->telefoon = $telefoon;
        return $this;
    }

    public function set_foto( string $foto ) : Member {
        $this->foto = $foto;
        return $this;
    }
}

?>