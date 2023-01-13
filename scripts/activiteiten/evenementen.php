<?php

/** Evenementen Container */
function create_evenementen_container( $volgende_evenementen, $evenement_type, $minimaal ) {
    $type_mapping = array(
        'algemeen'  => 'evenementen',
        'stage'     => 'stages',
        'sportkamp' => 'sportkampen'
    );
    $multiple_label = array_key_exists( $evenement_type, $type_mapping ) ? $type_mapping[$evenement_type] : 'evenementen';
    return create_activiteiten_container( $volgende_evenementen, $minimaal, $multiple_label );
}

?>