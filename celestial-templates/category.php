<?php
/**
 * The template for displaying Category pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */
get_header();  ?>

	<div id="left-content">
	<?php  //GET THEME HEADER CONTENT
	// Выводим заголовок текущей категории
	woffice_title( single_cat_title( '', false ) );
?>
		<?php  //GET THEME HEADER CONTENT
		$title = sprintf( __( 'Category Archives: <span>%s</span>', 'woffice' ), single_cat_title( '', false ));
		?> 	

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">
			<!-- START CONTENT -->
			<div id="content" class="">
				<?php	
				$show_title_box = woffice_get_reduxsettings_option('show_title_box');
				if(!woffice_validate_bool_option($show_title_box) ){
					echo sprintf('<h1 class="celst-page-title">%s</h1>',$title);
				} ?>

				<div class="row blog-main-row">
					<?php
					$blog_layout = woffice_get_theming_option('blog_layout');
					$blog_layout = (isset($_GET['blog_masonry'])) ? 'masonry' : $blog_layout;
					$masonry_columns = woffice_get_theming_option('masonry_columns',3);
					$content_type = woffice_get_theming_option('blog_listing_content','excerpt');
					if(get_post_type() == 'post'){
						$masonry_columns_class = 'masonry-layout--'.$masonry_columns.'-columns';

						echo ('masonry' === $blog_layout) ? '<div id="directory" class="masonry-layout '. $masonry_columns_class .'">' : '';
						$content_type = ('masonry' === $blog_layout) ? 'content-masonry' : 'content';
					}
					?>

					<?php
					/**
					 * Reset the query before displaying the post
					 * A loop must be unclosed before this call
					 * See: WOF-161
					 */
					wp_reset_query(); ?>

					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php // We check for the role : 
							if (woffice_is_user_allowed()) { ?>
								<!-- <?php //get_template_part( $content_type ); ?> -->
								<?php if (($content_type == "content-masonry")) {
                                    get_template_part( 'content-celestial-masonry' );
                                }
                                else {
                                    get_template_part( 'celestial' );
                                } ?>
							<?php } ?>
						<?php endwhile; ?>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>

					<?php echo ('masonry' === $blog_layout) ? '</div>' : ''; ?>

				</div>
				<!-- THE NAVIGATION --> 
				<?php woffice_paging_nav(); ?>
			</div>
		</div><!-- END #content-container -->

	</div><!-- END #left-content -->

<?php 
get_footer();
