<?php

function _create_evenement_suffix_infobar() : string {
    return '';
}

/** Evenementen Container */
function _create_evenementen_container( $volgende_evenementen, $evenement_type, $minimaal, $titel ) : string {
    $hoofdtitel_label = $titel ? _create_hoofdtitel_evenementen_container( $evenement_type ) : '';
    $multiple_label = _get_evenement_label( $evenement_type );
    return _create_activiteiten_container( $volgende_evenementen, $minimaal, $multiple_label, $hoofdtitel_label );
}

function _create_hoofdtitel_evenementen_container( string $evenement_type ) : string {
    $label = _get_evenement_label( $evenement_type );
    return 
        "<div class='d-flex small text-muted'>
            <h2><span class='me-3 strong'><i class='fas fa-glass-cheers'></i></span></h2>
            <h2><strong>Volgende $label</strong></h2>
        </div>";
}

function _get_evenement_label( $evenement_type ) : string {
    switch( $evenement_type ) {
        case 'algemeen': 
            $label = 'evenementen';
            break;
        case 'stage': 
            $label = 'stages';
            break;
        case 'sportkamp':
            $label = 'sportkampen';
            break;
        default:
            $label = 'evenementen';
            break;
    }
    return $label;
}

?>