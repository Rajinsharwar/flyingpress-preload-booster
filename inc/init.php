<?php

defined( 'ABSPATH' ) || exit;

/**
 * set 10 sec heartbeat
 */
add_filter( 'heartbeat_settings', 'flyingpress_preload_booster_set_heartbeat_time_interval', PHP_INT_MAX);

function flyingpress_preload_booster_set_heartbeat_time_interval( $settings ) {
    $settings['interval'] = 10;
    return $settings;
}


add_filter('wp_heartbeat_settings', 'flyingpress_preload_booster_set_heartbeat_frequency', PHP_INT_MAX);

function flyingpress_preload_booster_set_heartbeat_frequency( $settings ) {
    return [...$settings, 'interval' => 10];
}

add_action(
    'admin_bar_menu',
    function( $wp_admin_bar ) {
        if ( ! is_admin() ) {
            return;
        }

        $id   = 'preload_booster_indicator';
        $title = '<span class="fp-preload-booster-indicator">Preload Booster ON</span>';
        $href = esc_url( add_query_arg( array() ) );

        $wp_admin_bar->add_node(
            array(
                'id'    => $id,
                'title' => $title,
                'href'  => $href,
                'meta'  => array(
                    'title' => 'Preload Booster Active',
                ),
            )
        );
    },
    999
);

add_action(
    'admin_head',
    function() {
        ?>
        <style>
            .fp-preload-booster-indicator {
                background: #e07d00;
                color: #fff;
                padding: 0 8px 0 7px !important;
                border: 1px solid black;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.4;
                display: inline-block;
            }
        </style>
        <?php
    }
);