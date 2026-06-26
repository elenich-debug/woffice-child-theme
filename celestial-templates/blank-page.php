<?php

get_header(); 
$slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
?>



	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT 
			woffice_title(get_the_title());
		?>
			
		<?php // Start the Loop.
		while ( have_posts() ) : the_post(); ?>

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
				<?php if (!empty($slider_featured)) { ?>
					<div class="woffice-sliderev-wrapper mb-3">
						<?php woffice_get_page_slider();?>
					</div>
				<?php } ?>
				<?php 
				if (woffice_is_user_allowed()) {
					get_template_part( 'content', 'page' );
					
					$page_comments = woffice_get_theming_option('page_comments');
					// If comments are open or we have at least one comment, load up the comment template.
					if ( $page_comments){
						if ( comments_open() || get_comments_number()) {
							comments_template();
						}
					}
				}
				else { 
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
