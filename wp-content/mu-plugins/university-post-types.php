<?php

function university_post_types() {
    register_post_type( 'event', array(
        // re-save permalink options in admin area to 'activate' any new custom post type permalink settings and avoid 404 errors
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
}

add_action( 'init', 'university_post_types' );