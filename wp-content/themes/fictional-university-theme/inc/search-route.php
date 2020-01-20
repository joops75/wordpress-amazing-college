<?php

add_action( 'rest_api_init', 'universityRegisterSearch' );

function universityRegisterSearch() {
    register_rest_route( 'university/v1', 'search', array(
        'methods' => WP_REST_Server::READABLE, // returns 'GET'. Method is used as different hosts might use differing strings
        'callback' => 'universitySearchResults'
    ) );
}

function universitySearchResults($data) {
    $main_query = new WP_Query( array(
        'post_type' => array( 'post', 'page', 'professor', 'program', 'event' ),
        's' => sanitize_text_field( $data['term'] ) // 's' refers to the wp search function. 'term' is a url query parameter
    ) );

    $results = array();

    while( $main_query->have_posts() ) {
        $main_query->the_post();
        $postType = get_post_type();
        $eventDate = new DateTime( get_field( 'event_date' ) );
        if( !$results[$postType] ) $results[$postType] = array();
        array_push($results[$postType], infoArr($postType, $eventDate));
    }

    // custom queries

    if( !$results['program'] ) return $results;
    
    $programMetaQuery = array( 'relation' => 'OR' );

    foreach ($results['program'] as $program) {
        array_push($programMetaQuery, array(
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '' . $program['id'] . '"'
        ));
    }

    $programRelationshipQuery = new WP_Query( array(
        'post_type' => array( 'professor', 'event' ),
        'meta_query' => $programMetaQuery
    ) );

    while( $programRelationshipQuery->have_posts() ) {
        $programRelationshipQuery->the_post();
        $postType = get_post_type();
        $eventDate = new DateTime( get_field( 'event_date' ) );
        if( !$results[$postType] ) $results[$postType] = array();
        array_push($results[$postType], infoArr($postType, $eventDate));
        // remove duplicate results
        $results[$postType] = array_unique( $results[$postType], SORT_REGULAR );
    }

    wp_reset_postdata();

    return $results;
}

function infoArr($postType, $eventDate) {
    return array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink(),
        'authorName' => get_author_name(),
        'image' => get_the_post_thumbnail_url( 0, $postType . '-landscape' ),
        'month' => $postType === 'event' ? $eventDate->format('M') : false,
        'day' => $postType === 'event' ? $eventDate->format('d') : false,
        'description' => $postType === 'event' ? has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 20 ) : false,
        'id' => get_the_ID()
    );
}