<?php

/** Evenementen Container */
function _create_evenementen_container( $volgende_evenementen, $evenement_type, $minimaal ) : string {
    $type_mapping = array(
        'algemeen'  => 'evenementen',
        'stage'     => 'stages',
        'sportkamp' => 'sportkampen'
    );
    $multiple_label = array_key_exists( $evenement_type, $type_mapping ) ? $type_mapping[$evenement_type] : 'evenementen';
    return _create_activiteiten_container( $volgende_evenementen, $minimaal, $multiple_label );
}

?>