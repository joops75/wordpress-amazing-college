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
        get_template_part( 'template-parts/content', 'event' );
        endwhile;
        echo paginate_links();
    ?>

    <hr class="section-break">

    <p>Looking for a recap of past events? <a href="<?php echo site_url( '/past-events' ); ?>">Check out our past events archive</a>.</p>

</div>

<?php get_footer();