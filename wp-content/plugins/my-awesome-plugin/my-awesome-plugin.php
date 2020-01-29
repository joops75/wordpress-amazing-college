<?php

/*
Plugin Name: My Awesome Plugin
Description: Prepare for your mind to be blown!
*/

function loremCensorFunction($text) {
    return preg_replace( '/\b(l)ore(m)\b/i', '${1}***${2}', $text );
}

add_filter( 'the_content', 'loremCensorFunction' );

function programCountFunction() {
    $query = new WP_Query( array(
        'post_type' => 'program',
        'posts_per_page' => -1
    ) );

    return $query->found_posts;
}

add_shortcode( 'programCount', 'programCountFunction' ); // shortcode variables can be accessed via backend text provided they are put in brackets e.g. [programCount]