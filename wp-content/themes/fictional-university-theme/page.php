<?php
    get_header();
    while( have_posts() ) : the_post() ?>
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

            <?php
                $page_id = get_the_ID();
                $parent_id = wp_get_post_parent_id( $page_id );
                if( $parent_id ) :
            ?>

                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo get_permalink( $parent_id ); ?>"><i class="fa fa-home" aria-hidden="true"></i> <?php echo get_the_title( $parent_id ); ?></a> <span class="metabox__main"><?php the_title(); ?></span></p>
                </div>

            <?php endif; ?>

            <?php
                $children = get_pages( array( 'child_of' => $page_id ) );
                if ( $children or $parent_id) :
            ?>
            
                <div class="page-links">
                    <h2 class="page-links__title"><a href="<?php echo get_permalink( $parent_id ); ?>"><?php echo get_the_title( $parent_id ); ?></a></h2>
                    <ul class="min-list">
                        <?php wp_list_pages( array( 'title_li' => null, 'child_of' => $parent_id ? $parent_id : $page_id, 'sort_column' => 'menu_order' ) ); ?>
                    </ul>
                </div>

            <?php endif; ?>

            <div class="generic-content">
                <?php the_content(); ?>
            </div>

        </div>
    <?php endwhile; ?>
<?php
    get_footer();