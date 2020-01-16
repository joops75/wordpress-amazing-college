<?php
    get_header();
    pageBanner( array(
        'title' => 'All Events',
        'subtitle' => 'See what is going on in our world.'
    ) );
?>

<div class="container container--narrow page-section">
    <?php
        while( have_posts() ) : the_post();
        // posts are ordered and filtered by university_adjust_queries function in functions.php
        // this is so paginate_links will work as expected without any extra code
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
        echo paginate_links();
    ?>

    <hr class="section-break">

    <p>Looking for a recap of past events? <a href="<?php echo site_url( '/past-events' ); ?>">Check out our past events archive</a>.</p>

</div>

<?php get_footer();