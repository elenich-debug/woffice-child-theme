<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */
get_header();  
?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT
	// Автоматически получаем правильный заголовок для любого типа архива
	woffice_title( get_the_archive_title() );
?>

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content" >
			<?php	
				$show_title_box = woffice_get_reduxsettings_option('show_title_box');
				if(!woffice_validate_bool_option($show_title_box) ){
					echo sprintf('<h1 class="celst-page-title">%s</h1>',$title);
				} ?>
			<div class="row blog-main-row">
                <?php
                $blog_layout = woffice_get_theming_option('blog_layout');
				$blog_layout = (isset($_GET['blog_masonry'])) ? 'masonry' : $blog_layout;
				$content_type = ('masonry' === $blog_layout) ? 'content-celestial-masonry' : 'celestial';
                if(get_post_type() == 'post'){

                    echo ('masonry' === $blog_layout) ? '<div id="directory" class="masonry-layout">' : '';
	                $content_type = ('masonry' === $blog_layout) ? 'content-celestial-masonry' : 'celestial';
                }
                ?>

				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php // We check for the role :
						if ( woffice_is_user_allowed() ) {
							get_template_part( $content_type );
						}
						?>
					<?php endwhile; ?>
				<?php else : ?>
					<?php get_template_part( 'content', 'none' ); ?>
				<?php endif; ?>

                <?php echo ('masonry' === $blog_layout) ? '</div>' : ''; ?>

				<!-- THE NAVIGATION --> 
				<?php woffice_paging_nav(); ?>
			</div>
			</div>
		</div><!-- END #content-container -->
		
	</div><!-- END #left-content -->

<?php 
get_footer();
