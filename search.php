<?php
/**
 * The template for displaying Search Results pages
 */
get_header();  
?>

	<div id="left-content">
		<?php  //GET THEME HEADER CONTENT
		
		// --- НАЧАЛО ИЗМЕНЕНИЙ ---
		// Создаем HTML для нашего бэйджа с переводимым текстом
		$label = '<span class="search-results-label">' . __( 'Search Results for', 'woffice' ) . '</span>';
		// Безопасно получаем поисковый запрос
		$query = esc_html( get_search_query() );
		// Собираем финальный заголовок из бэйджа и запроса
		$title = $label . ' ' . $query;
		// --- КОНЕЦ ИЗМЕНЕНИЙ ---
		
		if(woffice_get_skin('classic')){
			woffice_top_navbar();
		} 
		woffice_title($title); // Передаем наш новый кастомный заголовок
		?>  	

		<!-- START THE CONTENT CONTAINER -->
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
				<div class="row blog-main-row search-blog-main-row">
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php  
							if(woffice_get_skin('celestial')){
								get_template_part( 'celestial' );
							} else {
								get_template_part( 'content' );
							}?>
						<?php endwhile; ?>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>

					<!-- THE NAVIGATION --> 
					<?php woffice_paging_nav(); ?>
				</div>
			</div>
				
		</div><!-- END #content-container -->

	</div><!-- END #left-content -->

<?php 
get_footer();