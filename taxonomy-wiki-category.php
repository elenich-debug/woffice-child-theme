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
?>

<?php // Start the Loop.

// We check for excluded categories
$wiki_excluded_categories = woffice_get_theming_option('wiki_excluded_categories');
/*If it's not a child only*/
$wiki_excluded_categories_ready = (!empty($wiki_excluded_categories)) ? $wiki_excluded_categories : array();
$enable_wiki_accordion = woffice_get_theming_option('enable_wiki_accordion');
$enable_wiki_accordion = ( $enable_wiki_accordion) ? true : false;
$sortbylike_option = woffice_get_theming_option('wiki_sortbylike');
$wiki_sortbylike = ($sortbylike_option && isset($_GET['sortby']) && $_GET['sortby']=='like') ? true : false ;

 ?>

	<div id="left-content">

		<?php  //GET THEME HEADER CONTENT

		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title =  $term->name;
		if(woffice_get_skin('classic')){
			woffice_top_navbar();
		} 
		woffice_title(get_the_title());
		?>

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container" class="wc">

			<!-- START CONTENT -->
			<div id="content">
				<?php if (true) { ?>

					<?php
					// CUSTOM CLASSES ADDED BY THE THEME
					$post_classes = array('box','content');
					$show_title_box = woffice_get_theming_option('show_title_box');
                    if(!woffice_validate_bool_option($show_title_box) ){
					?>
					<div class="post-title">
						<?php single_cat_title('<h1 class="post-title celst-page-title">','</h1>'); ?>
					</div>
					<?php } ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
						<div id="wiki-page-content" class="intern-padding wiki-item-row archive-wiki-item-row">
							<?php
							//DISABLED IN THIS THEME
							wp_link_pages(array('echo'  => 0));
							//EDIT LINK
							edit_post_link( __( 'Edit', 'woffice' ), '<span class="edit-link">', '</span>' );

							woffice_wiki_sort_by_like();

							if(class_exists('Woffice_Wiki_Display_Manager')){
								$wiki_display = new Woffice_Wiki_Display_Manager($term->term_id);

								$wiki_display->displayCategories();
							}

							woffice_wiki_display_actions_buttons();
							?>
						</div>

						<?php
						if (function_exists( 'woffice_wiki_extension_on' )){
							// CHECK IF USER CAN CREATE WIKI PAGE
							if ($woffice_role_allowed):  ?>

								<?php Woffice_Frontend::frontend_render('wiki', $process_result); ?>

							<?php endif;
						} ?>
					</article>

					<?php
				} else {
					get_template_part( 'content', 'private' );
				}
				?>
			</div>

		</div><!-- END #content-container -->

<?php // END THE LOOP
?>

<?php
get_footer();



