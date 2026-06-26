<?php
/**
 * The Template for displaying single event
 */
 
// UPDATE POST
global $post;

$edit_allowed = Woffice_Frontend::edit_allowed('woffice-event') == true;

if ($edit_allowed) {
    $process_result = Woffice_Frontend::frontend_process('woffice-event', $post->ID);
}

get_header();  ?>

<?php // Start the Loop.
while ( have_posts() ) : the_post(); ?>

    <div id="left-content">

        <?php  //GET THEME HEADER CONTENT

        woffice_top_navbar();
        woffice_title(get_the_title()); ?>

        <!-- START THE CONTENT CONTAINER -->
        <div id="content-container">
            <!-- START CONTENT -->
            <div id="content">
                <?php
                    $show_title_box = woffice_get_theming_option('show_title_box');
                    if(!woffice_validate_bool_option($show_title_box) ){
                ?>
                <div class="post-title event_single_title">
                    <?php the_title('<h1 class="post-title">','</h1>'); ?>
                </div>
                <?php
                    }
                    get_template_part( 'template-parts/content', 'single-event' );
                ?>

           <div class="event_comment_wrapper wo_common_comment_wrapper">
                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) {
	                comments_template();
                } ?>
                  </div>
            </div>

        </div><!-- END #content-container -->

    </div><!-- END #left-content -->
<?php // END THE LOOP
endwhile; ?>

<?php

get_footer();
