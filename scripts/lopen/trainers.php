<?php

require_once __DIR__ . '/../members/class-member.php';
require_once __DIR__ . '/../members/class-member-card-properties.php';

use Member\Member;
use Member\Member_Card_Properties;

function trainingsgroep_coordinator_func() {
    $coordinator = get_field( 'trainingsgroep_coordinator' );

    $coordinator_titel = strtolower(get_the_title( $coordinator ) );
    $coordinator_mail = get_field( 'clubfunctie_mail', $coordinator );
    $coordinator_leden = get_field( 'clubfunctie_lid', $coordinator );

    if( empty( $coordinator_leden ) ) {
        return '';
    }
    
    $coordinator_lid_id = $coordinator_leden[0];
    $coordinator_naam = get_the_title( $coordinator_lid_id );

    return 
        "<div>
            Vragen? Neem contact op met $coordinator_titel $coordinator_naam 
            (<a href='mailto:$coordinator_mail'>$coordinator_mail</a>).
        </div>";
}
add_shortcode( 'trainingsgroep_coordinator', 'trainingsgroep_coordinator_func' );

function trainingsgroep_trainers_container_func() {
    $trainers = array();
    $query = new WP_Query( _get_trainers_args() );
    if( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $trainer_id = get_field( 'trainingsopdracht_trainer' );
            $specialisatie = get_field( 'trainingsopdracht_specialisatie' );
            array_push( $trainers, _create_trainer( $trainer_id, $specialisatie ) );
        }
    }
    wp_reset_postdata();

    if( empty ( $trainers ) ) {
        return '';
    }

    $card_properties = new Member_Card_Properties();
    $card_properties->set_foto_aspect_ratio( 'rectangle' );
    $card_properties->set_foto_stretch( true );
    $card_properties->set_card_relative_width( 'col-lg-6' );

    $trainers_container = '';
    foreach ($trainers as $t => $trainer ) {
        $trainers_container .= $trainer->create_member_card( $card_properties );
    }

    return
        "<div class='trainers-container row'>
            $trainers_container
        </div>";
}
add_shortcode( 'trainingsgroep_trainers_container', 'trainingsgroep_trainers_container_func' );

function _get_trainers_args() : array {
    return array(
        'post_type'      => 'trainingsopdracht', 
        'posts_per_page' => -1,
        'meta_key'       => 'trainingsopdracht_trainingsgroep',
        'meta_value'     => get_the_ID(),
        'orderby'        => 'trainingsopdracht_seqNo',
        'order'          => 'ASC'
    );
}

function _create_trainer( int $trainer_id, string $specialisatie ) : Member {
    $trainer = new Member();
    $trainer->set_naam( get_the_title( $trainer_id ) );
    $fotoLink = get_field( 'trainer_fotoLink', $trainer_id );
    if( !empty( $fotoLink ) ) {
        $trainer->set_foto( get_field( 'trainer_fotoLink', $trainer_id ) );
    }
    $trainer->set_functie_beschrijving( $specialisatie );
    $mail = get_field( 'trainer_mail', $trainer_id );
    if( !empty( $mail ) ) {
        $trainer->set_mail( $mail );
    }
    return $trainer;
}

?>