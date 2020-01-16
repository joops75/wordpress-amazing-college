<?php

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