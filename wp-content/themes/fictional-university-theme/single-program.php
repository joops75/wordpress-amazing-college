<?php
    get_header();
    while( have_posts() ) : the_post();
?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg') ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php the_title(); ?></h1>
            <div class="page-banner__intro">
                <p>Replace me later.</p>
            </div>
        </div>  
    </div>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link( 'program' ); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a> <span class="metabox__main"><?php the_title(); ?></span></p>
        </div>

        <div class="generic-content">
            <?php the_content(); ?>
        </div>
        
        <?php
            $today = date('Ymd'); // ensure date format returned from backend for 'event_date' custom field is also 'Ymd'
            $eventPosts = new WP_Query( array(
                'post_type' => 'event',
                'posts_per_page' => -1,
                'meta_key' => 'event_date',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'event_date',
                        'compare' => '>=',
                        'value' => $today,
                        'type' => 'numeric' // not strictly necessary here
                    ),
                    array(
                        'key' => 'related_programs', // returns a stringified array from the database
                        'compare' => 'LIKE', // 'LIKE' means 'contains'
                        'value' => '"' . get_the_ID() . '"' // match value must contain "" for correct matching with stringified array
                    )
                )
            ) );
        ?>

        <?php if( $eventPosts->have_posts() ) : ?>

            <hr class="section-break">

            <h2 class="headline headline--medium">Upcoming <?php the_title(); ?> Events</h2>

        <?php
            endif;
            while( $eventPosts->have_posts() ) : $eventPosts->the_post();
            $eventDate = new DateTime( get_field( 'event_date' ) );
        ?>
            <div class="event-summary">
                <a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
                    <span class="event-summary__month"><?php echo $eventDate->format('M'); ?></span>
                    <span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>  
                </a>
                <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                    <p><?php echo has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 20 ); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more &raquo;</a></p>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        ?>

    </div>
<?php
    endwhile;
    get_footer();