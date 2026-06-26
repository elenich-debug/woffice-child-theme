<?php
/**
 * The Template for displaying all single project
 */
 
// UPDATE POST
global $post;

$project_edit = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'project_edit',true) : 'frontend-edit';

$process_result = array();
if ( $project_edit == 'frontend-edit' && is_user_logged_in() ) {
    $process_result = Woffice_Frontend::frontend_process('project', $post->ID);
}

get_header();  ?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">

			<?php  //GET THEME HEADER CONTENT
				 woffice_top_navbar();
				 woffice_title(get_the_title());
			 ?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">
				<!-- START CONTENT -->
				<div id="content">
					
					<?php
					if(woffice_is_user_allowed_projects()){
						get_template_part( 'template-parts/content', 'single-project' );
					} else {
						get_template_part( 'content', 'private' );
					}

					?>
				</div>
					
			</div><!-- END #content-container -->
		
		</div><!-- END #left-content -->
	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();