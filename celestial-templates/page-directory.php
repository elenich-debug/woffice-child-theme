<?php

$process_result = array();
$is_user_allowed   = woffice_is_user_allowed();
$slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
$page_id      = (is_home() && get_option('page_for_posts')) ? get_option('page_for_posts') : get_the_ID();


if (woffice_bp_is_buddypress()) {
	$bp_post_id = woffice_get_relative_current_buddypress_page_id();

	if ($bp_post_id) {
		$page_id = $bp_post_id;
	}
}

$slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');

if (function_exists( 'woffice_directory_extension_on' )){

	$directory_create = woffice_get_theming_option('directory_create'); 				
	if (Woffice_Frontend::role_allowed($directory_create)):

        $process_result = Woffice_Frontend::frontend_process('directory');
		
	endif;
	
}

get_header();  
$slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content" class="center-inner-container">

			<?php  //GET THEME HEADER CONTENT

				$title = get_the_title();
				
				woffice_title(get_the_title());
			?> 	

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">
				<!-- START CONTENT -->
				<div id="content">
				<?php
					$show_title_box = woffice_get_reduxsettings_option('show_title_box');
					if(!woffice_validate_bool_option($show_title_box) ){
				?>
					<?php the_title('<h1 class="celst-page-title">','</h1>'); ?>
				<?php } ?>
				<div class="celst-inner-search-wrapper celst-directory">
                    <div class="row">
                        <div class="col-md-6 left-content">
                            <?php  get_search_form(); ?>
                        </div>
                        <div class="col-md-6 right-content">
						<?php
							if (function_exists( 'woffice_directory_extension_on' )) {
								woffice_directory_filter();
							}
							?>
					<?php // CHECK IF USER CAN CREATE DIRECTORY ITEM
					$directory_create = woffice_get_theming_option('directory_create'); 
					if (Woffice_Frontend::role_allowed($directory_create)):  ?>
							<a href="" class="celst-add-btn celst-form-modal-toggle"><i class="woffice-icon woffice-icon-celst-plus"></i></a>
					<?php endif; ?>
                            
                        </div>
                    </div>
                </div>
					<?php if (!empty($slider_featured)) { ?>
						<div class="woffice-sliderev-wrapper mb-3">
							<?php woffice_get_page_slider();?>
						</div>
					<?php } ?>
					<?php
						$map_enabled = (is_page_template("page-templates/page-directory.php") || is_tax('directory-category')) && $is_user_allowed && empty($slider_featured);
						$map_enabled = apply_filters( 'woffice_directory_page_map_enabled', $map_enabled);
						$theme_settings_options = get_option('woffice_theme_options');
						$show_title_box = isset($theme_settings_options['show_title_box']) ? filter_var($theme_settings_options['show_title_box'], FILTER_VALIDATE_BOOLEAN) : false;
						if(!$show_title_box){
						if ($map_enabled) {
							$final_image = false;
							echo '<div id="map-directory"></div>';
						}
						
					?>
					<?php } ?>
                    
					<?php if (woffice_is_user_allowed()): ?>
					<?php /* If the directory extension is one we display the items */
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$args = array(
						'post_type' => 'directory',
						'paged' => $paged,
					);

                      /**
                       * Filter the args of the query for directory items
                       *
                       * @param array
                       */
					$directory_query = new WP_Query(apply_filters('woffice_directory_loop_args', $args, $paged));
					?>
					<div class="row directory-item-row">
					<?php
					if ( $directory_query->have_posts() && function_exists('woffice_directory_extension_on')) :
					
						// echo '<div id="directory" class="masonry-layout">';
						
						while($directory_query->have_posts()) : $directory_query->the_post();
					?>
							<div class="col-md-6 col-xl-4 col-celst-3 directory-col">
								<div class="directory-card-wrapper">
									
										<div class="directory-thumb">
											<?php
												if ( has_post_thumbnail() ) :
													Woffice_Frontend::render_featured_image_single_post($post->ID, '', $post->ID);
												endif; 
											?>
										</div>
										<div class="directory-card-body">
											<div class="directory-meta">
												<?php
													/* Categories */
													if( has_term('', 'directory-category')): 
														echo '<span class="directory-category">';
														echo get_the_term_list( $post->ID, 'directory-category', '', ' ' );
														echo '</span>';
													endif;

													/* Comments */
													if (comments_open() || get_comments_number()){
														echo'<span class="directory-comments"><i class="woffice-icon woffice-icon-celst-blog-comment"></i> ';
															echo'<a href="'. get_the_permalink().'#respond"><span>Comments</span>'. get_comments_number( '0', '1', '%' ) .'</a>';
															echo'</span>';	
													}
												?>
											</div>
											<?php
													echo'<h3 class="directory-title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h3>';
												?>
											<?php
												/* Excerpt */
												echo '<p class="directory-content">';
													echo woffice_directory_get_excerpt();
												echo '</p>';
											?>
											<?php woffice_directory_single_fields('page');?>
										</div>
									
								</div>
							</div>
						<?php
						endwhile;
								
						wp_reset_postdata();
						
						echo '</div>';
                        woffice_paging_nav($directory_query);
					
					else :
						
						get_template_part( 'content', 'none' );
						
					endif;  ?>
				 <div class="celts-frontend-form-wrapper">
					<?php // CHECK IF USER CAN CREATE DIRECTORY ITEM
					if (Woffice_Frontend::role_allowed($directory_create)):  ?>
							<?php Woffice_Frontend_Celestial::frontend_render('directory',$process_result); ?>	
					<?php endif; ?>
				</div>
				<?php
				else:
					get_template_part( 'content', 'private' );
				endif;
				?>
				</div>
				</div>
					
			</div><!-- END #content-container -->

	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();