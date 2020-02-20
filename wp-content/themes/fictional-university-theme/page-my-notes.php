<?php
    if( !is_user_logged_in() ) {
        wp_redirect( esc_url( site_url( '/' ) ) );
        exit;
    }
    get_header();
    while( have_posts() ) : the_post();
    pageBanner();    
?>
    <div class="container container--narrow page-section">
        <div class="create-note">
            <h2 class="headline headline--medium">Create New Note</h2>
            <input type="text" class="new-note-title" placeholder="Title">
            <input type="text" class="new-note-subtitle" placeholder="Subtitle">
            <textarea class="new-note-body" placeholder="Your note here..."></textarea>
            <input type="file" accept="image/*" class="new-note-image">
            <span class="submit-note">Create Note</span>
            <span class="note-limit-message"></span>
        </div>

        <ul class="min-list link-list" id="my-notes">
        <?php
            $userNotes = new WP_Query( array(
                'post_type' => 'note',
                'posts_per_page' => -1,
                'author' => get_current_user_id()
            ) );
            while( $userNotes->have_posts() ) : $userNotes->the_post();
        ?>
            <li data-id="<?php the_ID(); ?>">
                <input readonly class="note-title-field" type="text" value="<?php echo str_replace('Private: ', '', esc_attr( get_the_title() )); ?>">
                <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                <input readonly class="note-subtitle-field" type="text" value="<?php echo esc_attr( get_field( 'subtitle' ) ); ?>">
                <textarea readonly class="note-body-field"><?php echo esc_textarea( get_the_content() ); ?></textarea>
                <div class="note-image-area">
                    <img src="<?php echo get_the_post_thumbnail_url( 0, [150, 150] ); ?>" alt="<?php echo get_the_post_thumbnail_caption(); ?>">
                    <input type="file" accept="image/*" class="note-image-field update-note">
                </div>
                <span class="update-note btn btn--blue btn--small submit-update"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
            </li>
        <?php
            endwhile;
            wp_reset_postdata();
        ?>
        </ul>
    </div>
<?php
    endwhile;
    get_footer();