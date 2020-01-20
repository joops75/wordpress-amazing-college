<?php
    get_header();
    while( have_posts() ) : the_post();
    pageBanner();
?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link( 'program' ); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a> <span class="metabox__main"><?php the_title(); ?></span></p>
        </div>

        <div class="generic-content">
            <?php the_field( 'main_body_content' ); ?>
        </div>
        
        <?php
            $relatedProfessors = new WP_Query( array(
                'post_type' => 'professor',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'related_programs', // returns a stringified array from the database
                        'compare' => 'LIKE', // 'LIKE' means 'contains'
                        'value' => '"' . get_the_ID() . '"' // match value must contain "" for correct matching with stringified array
                    )
                )
            ) );
        ?>

        <?php if( $relatedProfessors->have_posts() ) : ?>

            <hr class="section-break">

            <h2 class="headline headline--medium"><?php the_title(); ?> Professors</h2>

            <ul class="professor-cards">

        <?php
            while( $relatedProfessors->have_posts() ) : $relatedProfessors->the_post();
        ?>
                <li class="professor-card__list-item">
                    <a class="professor-card" href="<?php the_permalink(); ?>">
                        <img src="<?php the_post_thumbnail_url('professor-landscape'); ?>" class="professor-card__image">
                        <span class="professor-card__name"><?php the_title(); ?></span>
                    </a>
                </li>
        <?php
            endwhile;
            wp_reset_postdata();
        ?>
            </ul>
        <?php endif; ?>
        
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
            get_template_part( 'template-parts/content', 'event' );
            endwhile;
            wp_reset_postdata();
        ?>

    </div>
<?php
    endwhile;
    get_footer();