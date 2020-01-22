<?php

function university_post_types() {
    // re-save permalink options in admin area to 'activate' any new custom post type permalink settings and avoid 404 errors

    // Event Post Type
    register_post_type( 'event', array(
        'capability_type' => 'event',
        'map_meta_cap' => true,
        'rewrite' => array( 'slug' => 'events' ),
        'has_archive' => true,
        'public' => true,
        'labels' => array(
            'name' => 'Events',
            'singular_name' => 'Event',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events'
        ),
        'menu_icon' => 'dashicons-calendar',
        'supports' => array( 'title', 'editor', 'excerpt' )
    ) );

    // Program Post Type
    register_post_type( 'program', array(
        'rewrite' => array( 'slug' => 'programs' ),
        'has_archive' => true,
        'public' => true,
        'labels' => array(
            'name' => 'Programs',
            'singular_name' => 'Program',
            'add_new_item' => 'Add New Program',
            'edit_item' => 'Edit Program',
            'all_items' => 'All Programs'
        ),
        'menu_icon' => 'dashicons-awards',
        'supports' => array( 'title' )
    ) );

    // Professor Post Type
    register_post_type( 'professor', array(
        'public' => true,
        'labels' => array(
            'name' => 'Professors',
            'singular_name' => 'Professor',
            'add_new_item' => 'Add New Professor',
            'edit_item' => 'Edit Professor',
            'all_items' => 'All Professors'
        ),
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array( 'title', 'editor', 'thumbnail' )
    ) );
}

add_action( 'init', 'university_post_types' );