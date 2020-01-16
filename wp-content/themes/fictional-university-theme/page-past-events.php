<?php
    get_header();
    pageBanner( array(
        'title' => 'Past Events',
        'subtitle' => 'A recap of our past events.'
    ) );
?>

<div class="container container--narrow page-section">
    <?php
        $today = date('Ymd'); // ensure date format returned from backend for 'event_date' custom field is also 'Ymd'
        $pastEvents = new WP_Query( array(
            'paged' => get_query_var( 'paged', 1 ),
            'post_type' => 'event',
            'meta_key' => 'event_date',
            'orderby' => 'meta_value_num',
            // 'order' => 'ASC', // default is 'DESC'
            'meta_query' => array(
                array(
                    'key' => 'event_date',
                    'compare' => '<',
                    'value' => $today,
                    'type' => 'numeric' // not strictly necessary here
                )
            )
        ) );
        while( $pastEvents->have_posts() ) : $pastEvents->the_post();
        get_template_part( 'template-parts/content', 'event' );
        endwhile;
        wp_reset_postdata();
        echo paginate_links( array(
            'total' => $pastEvents->max_num_pages
        ) );
    ?>
</div>

<?php get_footer();