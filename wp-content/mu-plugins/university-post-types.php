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

    // Note Post Type
    register_post_type( 'note', array(
        'capability_type' => 'note', // value doesn't need to match post type name
        'map_meta_cap' => true, // enforces permissions defined under 'note' capability_type in backend at the right time/place
        'show_in_rest' => true, // make data available in default rest api at /wp-json/wp/v2/note
        'public' => false, // don't want note post to show up in public queries or search results
        'show_ui' => true, // show post type in admin dashboard
        'labels' => array(
            'name' => 'Notes',
            'singular_name' => 'Note',
            'add_new_item' => 'Add New Note',
            'edit_item' => 'Edit Note',
            'all_items' => 'All Notes'
        ),
        'menu_icon' => 'dashicons-welcome-write-blog',
        'supports' => array( 'title', 'editor' )
    ) );

    // Like Post Type
    register_post_type( 'like', array(
        'public' => false, // don't want like post to show up in public queries or search results
        'show_ui' => true, // show post type in admin dashboard
        'labels' => array(
            'name' => 'Likes',
            'singular_name' => 'Like',
            'add_new_item' => 'Add New Like',
            'edit_item' => 'Edit Like',
            'all_items' => 'All Likes'
        ),
        'menu_icon' => 'dashicons-heart',
        'supports' => array( 'title' )
    ) );
}

add_action( 'init', 'university_post_types' );