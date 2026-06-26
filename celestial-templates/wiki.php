<?php

$wiki_create = woffice_get_theming_option('wiki_create');
$woffice_role_allowed = Woffice_Frontend::role_allowed($wiki_create, 'wiki');
$process_result = array();

if (function_exists( 'woffice_wiki_extension_on' )){

	if ($woffice_role_allowed):

        $process_result = Woffice_Frontend::frontend_process('wiki');
		
	endif;

}

get_header();  
$slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
?>

	<?php // Start the Loop.

    // We check for excluded categories
    
    /*If it's not a child only*/

	while ( have_posts() ) : the_post(); ?>

		<div id="left-content">
			<?php woffice_title(get_the_title()); ?>
			<?php  //GET THEME HEADER CONTENT

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
					<?php if (!empty($slider_featured)) { ?>
						<div class="woffice-sliderev-wrapper mb-3">
							<?php woffice_get_page_slider();?>
						</div>
					<?php } ?>
					<?php if (true) { ?>

						<?php 
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('content');
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>

							<div id="wiki-page-content" class="w9ki-page-content">
								<div class="celst-inner-search-wrapper celst-blog celst-wiki">
									<div class="row">
										<div class="col-md-6 left-content">
											<?php  get_search_form(); ?>
										</div>
										<div class="col-md-6 right-content">
											
											<?php 
											if (function_exists( 'woffice_wiki_extension_on' )){
											// CHECK IF USER CAN CREATE WIKI PAGE
											if ($woffice_role_allowed):  ?>
												<a href="" class="celst-add-btn celst-form-modal-toggle"><i class="woffice-icon woffice-icon-celst-plus"></i></a>
											<?php endif; 
													} ?>
										</div>
									</div>
								</div>
								<div class="row wiki-item-row">
									<?php
										$wiki_display = new Woffice_Wiki_Display_Manager(0);
		
										$wiki_display->displayCategories();
									?>
								</div>
								<?php 
								if (function_exists( 'woffice_wiki_extension_on' )){
									// CHECK IF USER CAN CREATE WIKI PAGE
									if ($woffice_role_allowed):  ?>

										<div class="celts-frontend-form-wrapper">

											<?php Woffice_Frontend_Celestial::frontend_render('wiki',$process_result); ?>

										</div>
										
									<?php endif; 
								} ?>
							</div>
							
						</article>

					<?php
					} else { 
						get_template_part( 'content', 'private' );
					}
					?>
				</div>
					
			</div><!-- END #content-container -->
	
	<?php // END THE LOOP 
	endwhile; ?>


<?php 
get_footer();
