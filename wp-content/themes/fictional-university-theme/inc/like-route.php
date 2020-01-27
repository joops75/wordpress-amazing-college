<?php

function universityLikeRoutes() {
    register_rest_route( 'university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'likeUnlike'
    ) );

    function likeUnlike($data) { // could alternatively pass in 'WP_REST_Request $request' as the argument
        // see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
        // $action = $request->get_param( 'action' );
        // $prof_id = $request->get_param( 'prof_id' );
        // $like_id = $request->get_param( 'like_id' );
        $action = sanitize_text_field( $data['action'] );
        $prof_id = sanitize_text_field( $data['prof_id'] );
        $like_id = sanitize_text_field( $data['like_id'] );

        if( is_user_logged_in() ) {
            if( get_post_type( $prof_id ) !== 'professor' ) {
                die( 'Invalid professor id.' );
            }
            $userLikedPosts = new WP_Query( array(
                'author' => get_current_user_id(), // Get_current_user_id returns 0 if user logged out, which effectively negates this line and so the query will run as if it wasn't present. Therefore, is_user_logged_in check is required
                'post_type' => 'like',
                'meta_query' => array(
                    array(
                        'key' => 'liked_professor_id',
                        'compare' => '=',
                        'value' => $prof_id
                    )
                )
            ) );
            if( $action === 'like' ) {
                if (!$userLikedPosts->found_posts) {
                    // return value of wp_insert_post is the id of the created post
                    return wp_insert_post( array(
                        'post_type' => 'like',
                        'post_status' => 'publish', // default is 'draft'
                        // 'post_title' => 'Test Like', // title field not necessary
                        
                        // add custom field values
                        'meta_input' => array(
                            'liked_professor_id' => $prof_id
                        )
                    ) );
                } else {
                    die( 'Only one like is allowed per user per post' );
                }
            } else {
                // get_current_user_id and get_post_field return different data types so don't use strict compare operator
                if( get_current_user_id() == get_post_field( 'post_author', $like_id ) and get_post_type( $like_id ) === 'like' ) {
                    wp_delete_post( $like_id, true );
                    return 'Like removed.';
                } else {
                    die( 'You do not have permission to delete that.' );
                }
            }
        } else {
            die( 'Only logged in users may submit like data.' );
        }
        
    }
}

add_action( 'rest_api_init', 'universityLikeRoutes' );