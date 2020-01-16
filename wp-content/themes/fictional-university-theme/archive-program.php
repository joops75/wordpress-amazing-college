<?php
    get_header();
    pageBanner( array(
        'title' => 'All Programs',
        'subtitle' => 'There is something for everyone. Have a look around.'
    ) );
?>

<div class="container container--narrow page-section">
    <ul class="link-list min-list">
        <?php
            while( have_posts() ) : the_post();
            // posts are ordered and filtered by university_adjust_queries function in functions.php
            // this is so paginate_links will work as expected without any extra code
        ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php
            endwhile;
            echo paginate_links();
        ?>
    </ul>

</div>

<?php get_footer();