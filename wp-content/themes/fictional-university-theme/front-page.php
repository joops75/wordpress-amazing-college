<?php get_header();  ?>

<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/library-hero.jpg'); ?>);"></div>
    <div class="page-banner__content container t-center c-white">
        <h1 class="headline headline--large">Welcome!</h1>
        <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
        <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
        <a href="<?php echo get_post_type_archive_link( 'program' ); ?>" class="btn btn--large btn--blue">Find Your Major</a>
    </div>
</div>

<div class="full-width-split group">
    <div class="full-width-split__one">
        <div class="full-width-split__inner">
            <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>

            <?php
                $today = date('Ymd'); // ensure date format returned from backend for 'event_date' custom field is also 'Ymd'
                $eventPosts = new WP_Query( array(
                    'post_type' => 'event',
                    'posts_per_page' => 2,
                    'meta_key' => 'event_date',
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'meta_query' => array(
                        array(
                            'key' => 'event_date',
                            'compare' => '>=',
                            'value' => $today,
                            'type' => 'numeric' // not strictly necessary here
                        )
                    )
                ) );

                while( $eventPosts->have_posts() ) : $eventPosts->the_post();
                get_template_part( 'template-parts/content', 'event' );
                endwhile;
                wp_reset_postdata();
            ?>
            
            <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link( 'event' ); ?>" class="btn btn--blue">View All Events</a></p>

        </div>
    </div>
    <div class="full-width-split__two">
        <div class="full-width-split__inner">
            <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>

            <?php
                $homePagePosts = new WP_Query( array( 'posts_per_page' => 2 ) );
                while( $homePagePosts->have_posts() ) : $homePagePosts->the_post();
            ?>
                <div class="event-summary">
                    <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>">
                        <span class="event-summary__month"><?php the_time( 'M' ); ?></span>
                        <span class="event-summary__day"><?php the_time( 'd' ); ?></span>  
                    </a>
                    <div class="event-summary__content">
                        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        <p><?php echo has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 20 ); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Read more &raquo;</a></p>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            ?>
            
            <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link( 'post' ); ?>" class="btn btn--yellow">View All Blog Posts</a></p>
        </div>
    </div>
</div>

<div class="hero-slider">
    <?php
        $slides = new WP_Query( array(
            'post_type' => 'hero_slide',
            'posts_per_page' => -1,
            'orderby'   => 'meta_value',
            'meta_key'  => 'description',
            'order' => 'DESC'
        ) );
        while( $slides->have_posts() ) : $slides->the_post();
        $description = get_field( 'description' );
        $background_image = get_field( 'background_image' );
        $link_url = get_field( 'link_url' );
    ?>
        <div class="hero-slider__slide" style="background-image: url(<?php echo $background_image['url']; ?>);">
            <div class="hero-slider__interior container">
                <div class="hero-slider__overlay">
                    <h2 class="headline headline--medium t-center"><?php the_title(); ?></h2>
                    <p class="t-center"><?php echo $description; ?></p>
                    <p class="t-center no-margin"><a href="<?php echo $link_url; ?>" class="btn btn--blue">Learn more</a></p>
                </div>
            </div>
        </div>
    <?php
        endwhile;
        wp_reset_postdata();
    ?>
</div>
<?php get_footer();