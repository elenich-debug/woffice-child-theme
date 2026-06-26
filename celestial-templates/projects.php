<?php

$process_result = array();

if (function_exists( 'woffice_projects_extension_on' )){

	$projects_create = woffice_get_theming_option('projects_create'); 				
	if (Woffice_Frontend_Celestial::role_allowed($projects_create)):
	
		$process_result = Woffice_Frontend_Celestial::frontend_process('project');
		
	endif;
	
}

$layout_class = '';
$layout_class = 'project-layout-grid';

get_header(); 
$slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">
			<?php woffice_title(get_the_title()); ?>
			
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
					<?php if (!empty($slider_featured)) { ?>
						<div class="woffice-sliderev-wrapper mb-3">
							<?php woffice_get_page_slider();?>
						</div>
					<?php } ?>
					<?php if (woffice_is_user_allowed()) { ?>
						<?php
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('box','content');
						?>
						<div id="post-<?php the_ID(); ?>" <?php post_class($layout_class);?>>
                            <?php if (function_exists('woffice_project_content_exists') && woffice_project_content_exists() ): ?>
									<div class="celst-inner-search-wrapper celst-project">
										<div class="row">
											<div class="col-xl-4 left-content">
												<?php  get_search_form(); ?>
											</div>
											<div class="col-xl-8 right-content">
													<?php 
														if (function_exists( 'woffice_projects_extension_on' )) {
															woffice_projects_filter();
														}
													
													$projects_create = woffice_get_theming_option('projects_create');
													if (Woffice_Frontend_Celestial::role_allowed($projects_create)): ?>
													<a href="" class="celst-add-btn celst-form-modal-toggle"><i class="woffice-icon woffice-icon-celst-plus"></i></a>
													<?php endif; ?>
											</div>
										</div>
									</div>
                            <?php endif; ?>

						<!-- LOOP ALL THE PROJECTS-->
						<?php // GET POSTS
						if (function_exists( 'woffice_projects_extension_on' )){

							$project_query_args = woffice_get_projects_loop_query_args();

							$project_query = new WP_Query($project_query_args);

							$project_query->posts = woffice_sort_projects_by_completion_date( $project_query->posts );

							if ( $project_query->have_posts() ) :

								// We check for the layout
								
								$projects_layout_class = '';
	
								$projects_layout_class .= 'view-group grid-view grid-layout--2-columns';
							
								echo'<ul id="projects-list" class="'. $projects_layout_class .' row p-0">';
								// LOOP
								while($project_query->have_posts()) : $project_query->the_post();

									get_template_part('celestial-content-parts/content', 'project');
								
								endwhile;
								echo '</ul>';
                        
							else :
								get_template_part( 'content', 'none' );
							endif;
							wp_reset_postdata();
							$projects_create = woffice_get_theming_option('projects_create'); 				
								if (function_exists( 'woffice_projects_extension_on' ) && Woffice_Frontend_Celestial::role_allowed($projects_create)):
						?>
							
						<?php woffice_paging_nav($project_query);?>
							
						<?php
							endif;
							// CHECK IF USER CAN CREATE PROJECT POST
							$projects_create = woffice_get_theming_option('projects_create');
							if (Woffice_Frontend_Celestial::role_allowed($projects_create)): ?>

                                <div class="celts-frontend-form-wrapper">

                                    <?php Woffice_Frontend_Celestial::frontend_render('project', $process_result); ?>

                                </div>

							<?php endif;

						 }?>

					<?php
					} else { 
						get_template_part( 'content', 'private' );
					}
					?>
					</div>
				</div>
					
			</div><!-- END #content-container -->
	
		</div><!-- END #left-content -->

	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();



