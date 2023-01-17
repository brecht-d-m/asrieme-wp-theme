<?php

function back_button_func( $atts ) {
    $sub_titel = get_field( 'pagina_slogan' );
    $heeft_terugknop = get_field( 'pagina_heeftTerugknop' );
    
    $terugknop = '';
    if( $heeft_terugknop ) {
        $terugknop_link = get_field( 'pagina_terugknopLink' );
        $terugknop_titel = get_field( 'pagina_terugknopTitel' );
        $terugknop = 
            "<div class='back-button-container'>
                <a href='$terugknop_link'>
                    <div class='icon'><i class='fas fa-chevron-left'></i></div>
                    <div class='titel animated'>$terugknop_titel</div>
                </a>
            </div>";
    }

    return 
        "<div class='d-flex align-items-center'>
            $terugknop
            <div class='pagina-subtitel'>$sub_titel</div>
        </div>";
}
add_shortcode( 'back_button', 'back_button_func' );

?>