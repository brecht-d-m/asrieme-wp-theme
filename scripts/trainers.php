<?php

require_once 'members/class-member.php';
require_once 'members/class-member-card-properties.php';
require_once 'members/members.php';

use Member\Member;
use Member\Member_Card_Properties;
use function Member\generate_member_card;

function trainingsgroep_coordinator_func() {
    $coordinator = get_field( 'trainingsgroep_coordinator' );

    $coordinator_titel = strtolower(get_the_title( $coordinator ));
    $coordinator_mail = get_field( 'clubfunctie_mail', $coordinator );
    $coordinator_leden = get_field( 'clubfunctie_lid', $coordinator );

    if ( empty( $coordinator_leden )) {
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
    $trainers_container = '';

    $args = array(
        'post_type'      => 'trainingsopdracht', 
        'posts_per_page' => -1,
        'meta_key'       => 'trainingsopdracht_trainingsgroep',
        'meta_value'     => get_the_ID(),
        'orderby'        => 'trainingsopdracht_seqNo',
        'order'          => 'ASC'
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $card_properties = new Member_Card_Properties();
        $card_properties->set_foto_aspect_ratio( 'rectangle' );
        $card_properties->set_card_relative_width( 'col-lg-6' );

        $trainer_cards = '';
        while ( $query->have_posts() ) {
            $query->the_post();
            $opdracht = array(
                'trainer'       => get_field( 'trainingsopdracht_trainer' ),
                'specialisatie' => get_field( 'trainingsopdracht_specialisatie' )
            );
            $trainer_cards .= _create_trainer_card( $opdracht, $card_properties );
        }
        $trainers_container = 
            "<div class='trainers-container row'>
                $trainer_cards
            </div>";
    }

    wp_reset_postdata();
    return $trainers_container;
}
add_shortcode( 'trainingsgroep_trainers_container', 'trainingsgroep_trainers_container_func' );

function _create_trainer_card( array $opdracht, Member_Card_Properties $card_properties ) {
    $trainer_id = $opdracht['trainer'];
    $naam = get_the_title( $trainer );

    $trainer = new Member();
    $trainer->set_naam( get_the_title( $trainer_id ) )
            ->set_foto( get_field( 'trainer_fotoLink', $trainer_id ) )
            ->set_functie_beschrijving( $opdracht['specialisatie'] )
            ->set_mail( get_field( 'trainer_mail', $trainer_id ) );
    return generate_member_card( $trainer, $card_properties );
}

?>