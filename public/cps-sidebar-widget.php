<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    cps
 * @subpackage cps/public/partials
 */

class CPS_Sidebar_Widget extends WP_Widget {

    function __construct() {

        parent::__construct(
            'cps_sidebar_widget',
            'CPS in Sidebar',
            array('description' => 'Show options in the selected location')
        );

    }

    function widget( $args, $instance ){
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];

        if( $title )
            echo $args['before_title'] . $title . $args['after_title'];


        if (!isset($_GET['cps_locations']) || !is_array($_GET['cps_locations']))
            return;

        $location_ids = $_GET['cps_locations'];

        $last_location_id = array_pop($location_ids);

        $last_location_object = get_term($last_location_id, 'cps_location');

        echo '<h4>Near ' . $last_location_object->name . '</h4>';

        $terms = get_terms( array(
            'taxonomy'      => array('cps_location'),
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'number'        => 5,
            'fields'        => 'all',
            'parent'        => $last_location_id,
        ) );

        foreach ($terms as $term) {
            echo '<a href="#" class="cps_widget_near">' . $term->name . '</a>';
        }

        echo $args['after_widget'];
    }

    // Сохранение настроек виджета (очистка)
    function update( $new_instance, $old_instance ) {
    }

    // html форма настроек виджета в Админ-панели
    function form( $instance ) {
    }

    function add_CPS_Sidebar_Widget_scripts() {
        if( ! apply_filters( 'show_CPS_Sidebar_Widget_script', true, $this->id_base ) )
            return;

        $theme_url = get_stylesheet_directory_uri();

        wp_enqueue_script('CPS_Sidebar_Widget_script', $theme_url .'/CPS_Sidebar_Widget_script.js' );
    }

    // стили виджета
    function add_CPS_Sidebar_Widget_style() {
        if( ! apply_filters( 'show_CPS_Sidebar_Widget_style', true, $this->id_base ) )
            return;
        ?>
        <style>
            .CPS_Sidebar_Widget a{ display:inline; }
        </style>
        <?php
    }
}

// Регистрация класса виджета
add_action( 'widgets_init', 'my_register_widgets' );
function my_register_widgets() {
    register_widget( 'CPS_Sidebar_Widget' );
}