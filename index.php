<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */


// CHECK IF USER CAN CREATE BLOG POST
$post_create = woffice_get_theming_option('post_create');
$woffice_role_allowed = Woffice_Frontend::role_allowed($post_create, 'post');
if ($woffice_role_allowed):

	$frontend_process = Woffice_Frontend::frontend_process('post');

endif;
$show_title_box = filter_var(woffice_get_theming_option('show_title_box'), FILTER_VALIDATE_BOOLEAN);
get_header();
?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT
			$title = woffice_get_theming_option('index_title');
			if(woffice_get_skin('classic')) {
				woffice_top_navbar();
				woffice_put_revslider();
				if($show_title_box){
					woffice_title($title);
				}
			}
		?>
			<?php  //GET THEME HEADER CONTENT
			$title = woffice_get_theming_option('index_title');
			if(woffice_get_skin('celestial')) {
				if($show_title_box){
					woffice_title($title);
				}
			}
		?>
		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
				<?php if(!$show_title_box){
						if(woffice_get_skin('classic')){ ?>
							<div class="row mb-5 align-items-center">
								<div class="col-md-6">
									<?php echo sprintf('<h1>%s</h1>',$title);?>
								</div>
							</div>
							<?php
						}// We check for the layout
					}
					if(woffice_get_skin('celestial')){ 
						$show_title_box = woffice_get_reduxsettings_option('show_title_box');
						if(!woffice_validate_bool_option($show_title_box) ){
							echo sprintf('<h1 class="celst-page-title">%s</h1>',$title);
						} ?>
					<div class="celst-inner-search-wrapper celst-blog">
                    <div class="row">
                        <div class="col-md-6 left-content">
                            <?php  get_search_form(); ?>
                        </div>
                        <div class="col-md-6 right-content">
                        <?php
                         // CHECK IF USER CAN CREATE BLOG POST
                        $post_create = woffice_get_theming_option('post_create'); 
                        if ($woffice_role_allowed): ?>
                            <a href="" class="celst-add-btn celst-form-modal-toggle"><i class="woffice-icon woffice-icon-celst-plus"></i></a>                                                        
                        <?php endif; ?>
                        </div>
                    </div>
                </div><?php
				} 
				$blog_layout = woffice_get_theming_option('blog_layout');
				$blog_layout = (isset($_GET['blog_masonry'])) ? 'masonry' : $blog_layout;
				$masonry_columns       = woffice_get_theming_option( 'masonry_columns',3);
				if (get_post_type() === 'post') {
					$masonry_columns_class = 'masonry-layout--' . $masonry_columns . '-columns';
				}


				if ($blog_layout === 'masonry' || isset($_GET['blog_masonry'])) { ?>
                    <div id="directory" class="masonry-layout <?php echo esc_html($masonry_columns_class); ?>">
                <?php } ?>
				<div class="row blog-main-row">
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php // We check for the role :
							if (woffice_is_user_allowed()) { ?>
								<?php if (($blog_layout == "masonry")) {
								
									if(woffice_get_skin('celestial')){
										get_template_part( 'content-celestial-masonry' );

									} else {
										get_template_part( 'content-masonry' );
									  }
								}
								else {
									if(woffice_get_skin('celestial')) {
										get_template_part( 'celestial','single' );
									} else {
										get_template_part( 'content','single' );
									}
								} ?>
							<?php } ?>
						<?php endwhile; ?>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>

				<?php if ($blog_layout === 'masonry' || isset($_GET['blog_masonry'])) { ?>
						</div>
					<?php } ?>
				</div>

				<!-- THE NAVIGATION -->
				<?php woffice_paging_nav(); ?>

				<?php
				/*
				 * FRONT END CREATION
				 */
				// CHECK IF USER CAN CREATE BLOG POST
				if ($woffice_role_allowed): ?>

					<div class="frontend-wrapper new_article_wrapper box celts-frontend-form-wrapper">
					<?php if(woffice_get_skin('celestial')){
	 					Woffice_Frontend_Celestial::frontend_render('post',$frontend_process); 

						} else {
							Woffice_Frontend::frontend_render('post',$frontend_process); 
						} ?>

					</div>

				<?php endif; ?>
			</div>

		</div><!-- END #content-container -->

	</div><!-- END #left-content -->

<?php
get_footer();
