<?php

require get_theme_file_path( '/inc/search-route.php' );
require get_theme_file_path( '/inc/like-route.php' );

function university_custom_rest() {
    register_rest_field( 'post', 'authorName', array(
        'get_callback' => function() { return get_the_author(); }
    ));
    
    register_rest_field( 'note', 'userNoteCount', array(
        'get_callback' => function() { return count_user_posts( get_current_user_id(), 'note' ); }
    ));

    register_meta('post', 'subtitle', [
        'object_subtype' => 'note', // restrict the usage of the 'subtitle' meta key to the 'note' post type in the rest api
        'show_in_rest' => true,
        'type'      => 'string', // Validate and sanitize the meta value as a string. Default: 'string'.
        'description'    => 'A meta key associated with a string meta value.', // Shown in the schema for the meta key.
        'single'        => true, // Return a single value of the type. Default: false.
    ]);
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($arr = null) { // could also pass in []
    if( !$arr['title'] ) $arr['title'] = get_the_title();
    if( !$arr['subtitle'] ) $arr['subtitle'] = get_field( 'page_banner_subtitle' );
    if( !$arr['image'] ) {
        $savedImage = get_field( 'page_banner_background_image' )['sizes']['page-banner'];
        if( $savedImage ) $arr['image'] = $savedImage;
        else $arr['image'] = get_theme_file_uri('/images/ocean.jpg');
    }
    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $arr['image']; ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $arr['title']; ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $arr['subtitle']; ?></p>
            </div>
        </div>  
    </div>
    <?php
}

function university_files() {
    wp_enqueue_script('university_js', get_theme_file_uri('/js/scripts-bundled.js'), null, microtime(), true);
    wp_enqueue_style('google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('random_name', get_stylesheet_uri(), null, microtime());
    wp_localize_script('university_js', 'universityData', array( // must use an existing script handle
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce( 'wp_rest' ) // used for authentication on front end
    ));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
    // use WP to control menus if desired
    // register_nav_menu( 'headerMenuLocation', 'Header Menu Location' );
    // register_nav_menu( 'footerMenuLocationOne', 'Footer Menu Location One' );
    // register_nav_menu( 'footerMenuLocationTwo', 'Footer Menu Location Two' );
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professor-landscape', 400, 260, true);
    add_image_size('professor-portrait', 480, 650, true);
    add_image_size('page-banner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query) {
    // Event query
    if( $query->is_main_query() and !is_admin() and is_post_type_archive( 'event' ) ) {
        // default wp query (not custom), not in admin area, on 'event' archive page
        $today = date('Ymd'); // ensure date format returned from backend for 'event_date' custom field is also 'Ymd'
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric' // not strictly necessary here
            )
        ));
    }
    
    // Program query
    if( $query->is_main_query() and !is_admin() and is_post_type_archive( 'program' ) ) {
        // default wp query (not custom), not in admin area, on 'program' archive page
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1); // display all posts
    }
}

add_action( 'pre_get_posts', 'university_adjust_queries' );

// redirect subscriber accounts out of admin and onto front page
function redirectSubsToFrontend() {
    $currentUser = wp_get_current_user();
    if( count( $currentUser->roles ) == 1 and $currentUser->roles[0] == 'subscriber' ) {
        wp_redirect( site_url( '/' ) );
        exit; // tells php to stop after redirect
    }
}

add_action( 'admin_init', 'redirectSubsToFrontend' );

// remove admin bar for subscribers
function noSubsAdminBar() {
    $currentUser = wp_get_current_user();
    if( count( $currentUser->roles ) == 1 and $currentUser->roles[0] == 'subscriber' ) {
        show_admin_bar( false );
    }
}

add_action( 'wp_loaded', 'noSubsAdminBar' );

// customize login image href url
function headerUrl() {
    return esc_url( site_url( '/' ) );
}

add_action( 'login_headerurl', 'headerUrl' );

// allow main stylesheet to be used in login area
function loginCSS() {
    wp_enqueue_style('google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('random_name', get_stylesheet_uri(), null, microtime());
}

add_action( 'login_enqueue_scripts', 'loginCSS' );

// customise login screen title
function loginTitle() {
    return get_bloginfo( 'name' );
}

add_action( 'login_headertitle', 'loginTitle' );

// amend note attributes before saving to the database
function customizeNote($data, $postArr) { // meta data is NOT passed in here
    if( $data['post_type'] === 'note') {
        // limit user note count
        if( count_user_posts( get_current_user_id(), 'note' ) >= 5 and !$postArr['ID'] ) { // if the post has no ID then it is being created
            die( "You have reached your note limit. Delete a note before creating another." ); // prevents all subsequent code from running and returns an error on front end
        }

        // escape input fields' html tags
        $data['post_title'] = sanitize_text_field( $data['post_title'] );
        $data['post_content'] = sanitize_textarea_field( $data['post_content'] );

        // make all notes that aren't being deleted private
        if( $data['post_status'] !== 'trash' ) {
            $data['post_status'] = 'private';
        }
    }
    return $data;
}

add_filter( 'wp_insert_post_data', 'customizeNote', 10, 2 ); // fires on creation and updating

function insert_note_meta($post, $request, $creating) { // meta data IS passed in here, fires after a single post is completely created or updated via the REST API
    $postId = wp_update_post( array(
        'ID'    => $post->ID,
        'meta_input' => array(
            'subtitle' => sanitize_text_field( $request['subtitle'] )
        )
    ) );

    if ( false === $postId ) {
        return new WP_Error(
            'rest_note_subtitle_failed',
            __( 'Failed to update note subtitle.' ),
            array( 'status' => 500 )
        );
    }

    return true;
}

add_action( 'rest_after_insert_note', 'insert_note_meta', 11, 3 ); // 'rest_after_insert_' . 'post_type_here'
